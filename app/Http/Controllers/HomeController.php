<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Post;
use App\Models\Sponsor;
use App\Services\ModuleService;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

    public function chatbotAuto()
    {
        $question = request('question', 'Xin chào');
        $type = request('type', 'not_train');
        $response = Http::timeout(120)->withHeaders([
            'SERVICE-KEY' => "chatbotserverme-key",
        ])->post("http://103.162.30.171:8001/api/chat", [
            'question' => $question,
            'type' => $type,
        ]);
        if ($response->successful()) {
            // If the chatbot service responded successfully, return its JSON response directly
            return response()->json([
                'status' => 'success',
                'message' => $response->json() // Return the JSON data from the chatbot service
            ]);
        } else {
            // If the chatbot service failed, return a JSON error message
            // You can include the chatbot's status code and potential error body for debugging
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get a successful response from chatbot service.',
                'chatbot_status' => $response->status(),
                'chatbot_error_body' => $response->body() // Get the raw body for more details
            ], $response->status()); // Return the same status code as the chatbot if applicable
        }
    }
}
