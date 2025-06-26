<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $sponsors = Sponsor::orderBy('created_at', 'desc')->paginate(10);
        return view('sponsors.index', compact('sponsors'));
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
            'status' => ['required', Rule::in(['active', 'inactive', 'pending'])],
            'description' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('sponsors', 'public');
        }

        Sponsor::create([
            'name' => $request->name,
            'image' => $imagePath,
            'status' => $request->status,
            'description' => $request->description, // Lưu mô tả
        ]);

        return redirect()->route('sponsors.index')->with('success', 'Nhà tài trợ đã được thêm thành công.');
    }

    /**
     * Show the form for editing the specified sponsor.
     */
    public function edit(Sponsor $sponsor)
    {
        $modules = Module::all(); // Lấy tất cả module để liên kết
        // Lấy các module mà nhà tài trợ này đang tài trợ, kèm theo pivot data
        $sponsoredModules = $sponsor->modules->keyBy('id');

        return view('sponsors.edit', compact('sponsor', 'modules', 'sponsoredModules'));
    }

    /**
     * Update the specified sponsor in storage.
     */
    public function update(Request $request, Sponsor $sponsor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => ['required', Rule::in(['active', 'inactive', 'pending'])],
            'description' => 'nullable|string',
        ]);

        $imagePath = $sponsor->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('sponsors', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        $sponsor->update([
            'name' => $request->name,
            'image' => $imagePath,
            'status' => $request->status,
            'description' => $request->description,
        ]);

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


        return redirect()->route('sponsors.index')->with('success', 'Nhà tài trợ đã được cập nhật thành công.');
    }

    /**
     * Remove the specified sponsor from storage.
     */
    public function destroy(Sponsor $sponsor)
    {
        if ($sponsor->image) {
            Storage::disk('public')->delete($sponsor->image);
        }
        $sponsor->delete();
        return redirect()->route('sponsors.index')->with('success', 'Nhà tài trợ đã được xóa thành công.');
    }
}
