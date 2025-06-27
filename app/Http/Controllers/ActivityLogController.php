<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view activity logs'])->only('index');
    }

    public function index(Request $request)
    {
        $query = Activity::query();
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        $activities = $query->latest()->paginate(20);

        $users = User::orderBy('name')->get(['id', 'name']);
        $oldInput = $request->all();

        return view('activity_log.index', compact('activities', 'users', 'oldInput'));
    }
}
