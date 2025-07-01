<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
        $this->middleware(['auth', 'permission:view modules'])->only('index');
        $this->middleware(['auth', 'permission:edit modules'])->only('edit', 'update');
        $this->middleware(['auth', 'permission:show modules'])->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modules = $this->moduleService->getAllModules();
        $status = $this->moduleService->getStatus();
        return view('modules.index', compact('modules', 'status'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        $statuses = $this->moduleService->getStatus();
        return view('modules.edit', compact('module', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('modules')->ignore($module->id),
            ],
            'code' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('modules')->ignore($module->id),
            ],
            'description' => 'nullable|string|max:1000',
            'status' => ['required', 'integer', 'in:' . implode(',', array_keys($this->moduleService->getStatus()))],
        ]);

        $this->moduleService->updateModule($module, $request->all());

        return redirect()->route('modules.index')->with('success', __('Module updated successfully!'));
    }

    /**
     * Display the specified module.
     * (Đây là trang xem chi tiết mà user sẽ click vào từ thông báo)
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\View\View
     */
    public function show(Module $module)
    {
        if ($module->status !== $this->moduleService::STATUS_PUBLISHED) {
            abort(404);
        }
        return view('modules.show', compact('module'));
    }
}
