<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotSetting;
use App\Services\ChatbotService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Hiển thị trang cài đặt chatbot.
     */
    public function index()
    {
        $botNameSetting = ChatbotSetting::where('key', 'bot_name')->first();
        $subjectContentSetting = ChatbotSetting::where('key', 'subject_content')->first();

        return view('chatbot.settings', compact(
            'botNameSetting',
            'subjectContentSetting',
        ));
    }

    /**
     * Xử lý cập nhật cài đặt chatbot.
     */
    public function update(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'bot_name' => 'required|string|max:255',
            'subject_content' => 'required|string',
        ]);

        // 2. Cập nhật từng cài đặt
        try {
            ChatbotSetting::updateOrCreate(
                ['key' => 'bot_name'],
                ['value' => $validatedData['bot_name'], 'type' => 'string']
            );

            $trainingResult = $this->chatbotService->trainContent($validatedData['subject_content']);

            if (isset($trainingResult['error'])) {
                \Log::error("ChatbotService: Training failed after update: " . ($trainingResult['details'] ?? 'Unknown error'));
                return redirect()->back()->with('error', __('Chatbot settings updated, but training failed: ') . ($trainingResult['details'] ?? 'Vui lòng kiểm tra log.'));
            } else {
                ChatbotSetting::updateOrCreate(
                    ['key' => 'subject_content'],
                    ['value' => $validatedData['subject_content'], 'type' => 'longtext']
                );
                \Log::info("ChatbotService: Training initiated successfully after update.", $trainingResult);
            }

            return redirect()->route('chatbot.setting.index')->with('success', __('Chatbot settings updated successfully!'));
        } catch (\Exception $e) {
            // Ghi log lỗi để debug
            \Log::error("Failed to update chatbot settings: " . $e->getMessage());
            return redirect()->back()->with('error', __('Failed to update chatbot settings. Please try again.'));
        }
    }
}
