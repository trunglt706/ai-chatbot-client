<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Post;
use App\Models\Sponsor;
use App\Services\ModuleService;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display the home page with published modules, posts, and user activity.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $publishedModules = Module::where('status', ModuleService::STATUS_PUBLISHED)
            ->latest()
            ->take(5)
            ->get();

        $publishedPosts = Post::where('is_published', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        $userActivities = collect();
        if (Auth::check()) {
            $userActivities = Activity::where('causer_id', Auth::id())
                ->where('causer_type', 'App\\Models\\User')
                ->latest()
                ->take(5)
                ->get();
        }

        // Lấy danh sách nhà tài trợ đang hoạt động
        $activeSponsors = Sponsor::where('status', 'active')->get();

        return view('dashboard', compact('publishedModules', 'publishedPosts', 'userActivities', 'activeSponsors'));
    }
}
