<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotMessage;
use App\Models\Chatbot\ChatbotSetting;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HomeController extends Controller
{
    protected $chatbotService;

    // Inject ChatbotService vào constructor
    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function index()
    {
        return view('chatbot.index');
    }

    public function demo()
    {
        $botNameSetting = ChatbotSetting::where('key', 'bot_name')->first();
        if (empty($botNameSetting->value)) {
            $this->chatbotService->active();
        }
        $subjectContent = ChatbotSetting::where('key', 'subject_content')->value('value');
        $userQuotaLimit = ChatbotSetting::where('key', 'token_limit')->value('value');
        return view('chatbot.demo', compact('subjectContent', 'userQuotaLimit', 'botNameSetting'));
    }

    public function history(Request $request)
    {
        // Kiểm tra quyền truy cập người dùng
        $userId = Auth::id();

        $page = request('page', 1); // Trang hiện tại, mặc định là 1
        $perPage = 10; // Số tin nhắn mỗi trang, bạn có thể cấu hình số này

        $offset = ($page - 1) * $perPage;

        // Lấy tổng số tin nhắn của người dùng để xác định còn dữ liệu hay không
        $totalMessages = ChatBotMessage::whereUserId($userId)->count();

        // Lấy tin nhắn
        $messages = ChatBotMessage::whereUserId($userId)->whereId(0)
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->get(['type', 'content'])
            ->reverse() // Đảo ngược để có thứ tự thời gian tăng dần
            ->values(); // Đảm bảo collection có các key số nguyên liên tiếp sau reverse

        // Kiểm tra xem còn tin nhắn cũ hơn sau trang hiện tại hay không
        // Logic: Tổng số tin nhắn > (số tin nhắn đã bỏ qua + số tin nhắn trên trang hiện tại)
        $hasMore = ($totalMessages > ($offset + $messages->count()));

        // Trả về StreamedResponse để gửi từng tin nhắn dưới dạng SSE
        return new StreamedResponse(function () use ($messages, $hasMore) {
            // Set headers cho Server-Sent Events
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no'); // Đối với Nginx, ngăn đệm phản hồi

            foreach ($messages as $message) {
                // Đảm bảo nội dung không có các ký tự xuống dòng thừa hoặc định dạng lạ
                $cleanedContent = str_replace(["\n\n", "\n"], " ", $message->content); // Thay thế double newline và single newline bằng khoảng trắng
                $cleanedContent = preg_replace('/\s+/', ' ', $cleanedContent); // Loại bỏ nhiều khoảng trắng liên tiếp
                $cleanedContent = trim($cleanedContent); // Xóa khoảng trắng đầu/cuối

                $data = [
                    'sender' => $message->type ?? 'system', // Dùng 'type' từ DB, fallback 'system'
                    'text' => $cleanedContent,
                ];

                // Định dạng SSE: data: <json_data>\n\n
                // Sử dụng JSON_UNESCAPED_UNICODE để giữ nguyên ký tự tiếng Việt
                echo "data: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";

                ob_flush();
                flush();
            }

            // Gửi một sự kiện đặc biệt báo hiệu kết thúc dữ liệu của trang hiện tại
            echo "data: " . json_encode(['done' => true, 'hasMore' => $hasMore]) . "\n\n";
            ob_flush();
            flush();
        }, 200); // Đảm bảo status code là 200 OK
    }

    public function speechToText() {}

    /**
     * Xử lý gửi tin nhắn từ người dùng và nhận phản hồi từ bot (qua SSE).
     */
    public function send(Request $request)
    {
        ob_implicit_flush(true);

        $message = $request->input('message', '');
        if (empty($message)) {
            return response()->stream(function () {
                echo "retry: 1000\n";
                echo "data: <span class=''>Xin chào, tôi có thể giúp gì cho bạn!</span>\n\n";
                echo "data: [DONE]\n\n";
                ob_flush();
                flush();
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
            ]);
        }

        $ip = $request->ip();
        $userId = Auth::id();

        // Ghi lại tin nhắn của người dùng
        ChatbotMessage::create([
            'user_id' => $userId,
            'ip_address' => $ip,
            'content' => $message,
            'type' => 'user',
            'status' => 'sent'
        ]);

        // Xử lý quota bằng ChatbotService
        $quotaResponse = $this->chatbotService->handleQuota($message, $ip, $userId);

        if ($quotaResponse !== null) {
            return response()->stream(function () use ($quotaResponse) {
                foreach ($quotaResponse as $line) {
                    echo $line;
                }
                ob_flush();
                flush();
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
            ]);
        }

        return response()->stream(function () use ($message, $userId, $ip) {
            $botResponseAccumulator = '';
            foreach ($this->chatbotService->streamChatResponse($message) as $chunk) {
                echo $chunk; // Gửi chunk này đến frontend

                // Kiểm tra nếu đây là một dòng dữ liệu (không phải [DONE] hoặc các dòng khác)
                // và không phải là dòng trống rỗng chỉ chứa "data: "
                if (str_starts_with($chunk, 'data: ') && !str_contains($chunk, '[DONE]')) {
                    $actual_data = substr($chunk, 6);
                    // Chỉ nối vào nếu có dữ liệu thực sự
                    if (!empty($actual_data)) {
                        $botResponseAccumulator .= $actual_data;
                    }
                }

                // Đảm bảo flush để đẩy dữ liệu ra ngay lập tức
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }

            $plainBotResponseContent = strip_tags($botResponseAccumulator);
            ChatbotMessage::create([
                'user_id' => $userId,
                'ip_address' => $ip,
                'content' => $plainBotResponseContent,
                'type' => 'bot',
                'status' => 'sent',
            ]);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }
}
