<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SponsorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view sponsors'])->only('index', 'show');
        $this->middleware(['auth', 'permission:create sponsors'])->only('create', 'store');
        $this->middleware(['auth', 'permission:edit sponsors'])->only('edit', 'update');
        $this->middleware(['auth', 'permission:delete sponsors'])->only('destroy');
    }

    /**
     * Display a listing of the sponsors.
     */
    public function index()
    {
        $search = request('search', '');
        $pageSize = request('pageSize', 10);

        $sponsors = Sponsor::query();
        $sponsors = $search ? $sponsors->search($search) : $sponsors;
        $sponsors = $sponsors->paginate($pageSize);
        return view('sponsors.index', compact('sponsors', 'search'));
    }

    /**
     * Show the form for creating a new sponsor.
     */
    public function create()
    {
        return view('sponsors.create');
    }

    /**
     * Store a newly created sponsor in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'description' => 'nullable|string',
        ]);

        $sponsor = Sponsor::create([
            'name' => $request->name,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        // Handle image upload via media relation
        if ($request->hasFile('image')) {
            $sponsor->addMediaFromRequest('image')->toMediaCollection('sponsor_logo');
        }

        return redirect()->route('sponsors.index')->with('success', __('The sponsor has been added successfully.'));
    }

    /**
     * Show the form for editing the specified sponsor.
     */
    public function edit(Sponsor $sponsor)
    {
        $sponsor->load('modules');
        // Lấy tất cả các module
        $allModules = Module::all();

        // Lấy ID của các module mà nhà tài trợ này đã tài trợ
        $sponsoredModuleIds = $sponsor->modules->pluck('id')->toArray();

        // Lọc ra các module mà nhà tài trợ này chưa tài trợ
        $availableModules = $allModules->reject(function ($module) use ($sponsoredModuleIds) {
            return in_array($module->id, $sponsoredModuleIds);
        });

        return view('sponsors.edit', compact('sponsor', 'availableModules'));
    }

    /**
     * Update the specified sponsor in storage.
     */
    public function update(Request $request, Sponsor $sponsor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'description' => 'nullable|string',
        ]);

        $sponsor->update([
            'name' => $request->name,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        // Handle image update via media relation
        if ($request->hasFile('image')) {
            $sponsor->clearMediaCollection('sponsor_logo');
            $sponsor->addMediaFromRequest('image')->toMediaCollection('sponsor_logo');
        } elseif ($request->boolean('remove_image')) {
            $sponsor->clearMediaCollection('sponsor_logo');
        }

        // Cập nhật mối quan hệ với modules
        $syncData = [];
        foreach ($request->input('modules', []) as $moduleId => $moduleData) {
            if (isset($moduleData['is_sponsored']) && $moduleData['is_sponsored']) {
                $syncData[$moduleId] = [
                    'total_amount' => $moduleData['total_amount'] ?? 0,
                    'start_date' => $moduleData['start_date'] ?? null,
                    'end_date' => $moduleData['end_date'] ?? null,
                ];
            }
        }
        $sponsor->modules()->sync($syncData);


        return redirect()->route('sponsors.index')->with('success', __('The sponsor has been updated successfully.'));
    }

    /**
     * Remove the specified sponsor from storage.
     */
    public function destroy(Sponsor $sponsor)
    {
        $sponsor->clearMediaCollection('sponsor_logo');
        $sponsor->delete();
        return redirect()->route('sponsors.index')->with('success', __('The sponsor has been deleted successfully.'));
    }

    /**
     * Attach a new module sponsorship to the specified sponsor.
     */
    public function attachModule(Request $request, Sponsor $sponsor)
    {
        $request->merge([
            'total_amount' => currency_to_number($request->input('total_amount')),
        ]);
        $validatedData = $request->validate([
            'module_id' => [
                'required',
                'exists:modules,id',
                Rule::unique('module_sponsor')->where(function ($query) use ($sponsor) {
                    return $query->where('sponsor_id', $sponsor->id);
                }),
            ],
            'total_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'module_id.unique' => __('This module is already sponsored by this sponsor.'),
            'end_date.after_or_equal' => __('The end date must be after or equal to the start date.'),
        ]);

        // Gắn (attach) module vào sponsor với các thông tin pivot
        $sponsor->modules()->attach($validatedData['module_id'], [
            'total_amount' => $validatedData['total_amount'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
        ]);

        $module = Module::find($validatedData['module_id']);

        activity()
            ->performedOn($sponsor) // Hành động này được thực hiện trên Sponsor
            ->causedBy(auth()->user()) // Người dùng hiện tại đã gây ra hành động này
            ->withProperties([
                'module_id' => $module->id,
                'module_name' => $module->name,
                'total_amount' => $validatedData['total_amount'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
            ])
            ->log('Attached module sponsorship to sponsor');

        return redirect()->back()->with('success', __('Module sponsorship added successfully.'));
    }

    /**
     * Cancel a module sponsorship for the specified sponsor.
     * This will detach the module from the sponsor.
     */
    public function cancelModuleSponsorship(Request $request, Sponsor $sponsor, Module $module)
    {
        $pivotData = $sponsor->modules()->where('module_id', $module->id)->first();

        // Kiểm tra xem mối quan hệ có tồn tại không
        if (!$pivotData) {
            return redirect()->back()->with('error', __('Sponsorship not found.'));
        }

        $oldPivotData = $pivotData->pivot->toArray();

        // Hủy tài trợ (xóa khỏi bảng pivot)
        $sponsor->modules()->detach($module->id);

        activity()
            ->performedOn($sponsor)
            ->causedBy(auth()->user())
            ->withProperties([
                'module_id' => $module->id,
                'module_name' => $module->name,
                'total_amount' => $oldPivotData['total_amount'],
                'start_date' => $oldPivotData['start_date'],
                'end_date' => $oldPivotData['end_date'],
            ])
            ->log('Cancelled module sponsorship from sponsor');
        return redirect()->back()->with('success', __('Sponsorship cancelled successfully.'));
    }
}
