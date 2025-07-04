<?php

namespace App\Services;

use App\Models\Chatbot\ChatbotMessage;
use App\Models\Chatbot\ChatbotSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    protected $serviceUrl;
    protected $serviceKey;
    protected $userQuotaLimit;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->serviceUrl = 'http://103.162.30.171:8000/api';
        $this->serviceKey = 'trunglt706key';
        $this->userQuotaLimit = (int) (ChatbotSetting::where('key', 'token_limit')->value('value') ?? env('CHATBOT_DEFAULT_QUOTA', 20));
    }

    /**
     * Kích hoạt và kiểm tra các cài đặt cần thiết cho chatbot.
     * Tạo các cài đặt mặc định nếu chúng chưa tồn tại.
     *
     * @return void
     */
    public function active(): void
    {
        $defaultSettings = [
            'bot_name' => [
                'value' => 'Mona Bot',
            ],
            'subject_content' => [
                'value' => '',
            ],
            'token_limit' => [
                'value' => 20,
            ],
        ];

        foreach ($defaultSettings as $key => $data) {
            ChatbotSetting::firstOrCreate(
                ['key' => $key],
                [
                    'value' => $data['value'],
                ]
            );
        }
    }

    public static function emptyMessage()
    {
        return response()->stream(function () {
            echo "data: Xin chào,\n\n";
            flush();
            echo "data:  tôi là <b>MONA BOT</b>\n\n";
            flush();
            echo "data: , tôi có thể giúp được gì cho bạn?\n\n";
            echo "data: [DONE]\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    public static function getQuotaKey()
    {
        return 'chatbot_quota:' . request()->ip(); // hoặc request()->user()?->id nếu dùng auth
    }

    public static function checkIncreaseQuota($message)
    {
        return strtolower(trim($message)) === 'ok';
    }

    public static function highlightAnswer($answer, $keywords)
    {
        foreach ($keywords as $keyword) {
            $pattern = '/' . preg_quote($keyword, '/') . '/i';
            $answer = preg_replace_callback($pattern, function ($match) {
                return '<span class="highlight">' . $match[0] . '</span>';
            }, $answer);
        }
        return $answer;
    }

    public static function extractKeywords($question)
    {
        $words = explode(' ', strtolower($question));
        return array_filter($words, function ($word) {
            return mb_strlen($word) > 3;
        });
    }

    public static function responseDenied($message, $route = 'chatbot.setting.index')
    {
        if (request()->expectsJson()) {
            return response()->json([
                'status' => false,
                'message' => $message,
            ]);
        }
        return redirect()->route($route)->with('error', $message);
    }

    /**
     * Gửi tin nhắn đến dịch vụ chatbot và nhận phản hồi.
     *
     * @param string $question Câu hỏi của người dùng.
     * @param string $model Mô hình AI (ví dụ: 'gpt-3.5', 'llama2').
     * @param string $type Loại cuộc hội thoại (ví dụ: 'text', 'code').
     * @return array|null Phản hồi từ bot hoặc null nếu có lỗi.
     */
    public function sendMessage(string $question, string $model = 'default', string $type = 'text'): ?array
    {
        if (empty($this->serviceUrl) || empty($this->serviceKey)) {
            Log::warning('ChatbotService: Cannot send message. Service URL or Key is missing.');
            return ['error' => 'Chatbot service not configured.'];
        }

        $endpoint = rtrim($this->serviceUrl, '/') . '/chat'; // Đảm bảo đường dẫn đúng

        try {
            $response = Http::withHeaders([
                'SERVICE-KEY' => $this->serviceKey,
            ])->post($endpoint, [
                'question' => $question,
                'model' => $model,
                'type' => $type,
            ]);

            if ($response->successful()) {
                Log::info('ChatbotService: Message sent successfully.', ['response' => $response->json()]);
                return $response->json();
            } else {
                Log::error('ChatbotService: Failed to send message.', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Failed to get response from chatbot service.', 'details' => $response->body()];
            }
        } catch (\Exception $e) {
            Log::error('ChatbotService: Exception when sending message: ' . $e->getMessage());
            return ['error' => 'An error occurred while sending message.', 'details' => $e->getMessage()];
        }
    }

    /**
     * Gửi nội dung huấn luyện mới đến dịch vụ chatbot.
     *
     * @param string $newContent Nội dung huấn luyện mới.
     * @param bool $forceUpdate Buộc gửi cập nhật dù nội dung không đổi.
     * @return array|null Phản hồi từ bot hoặc null nếu có lỗi.
     */
    public function trainContent(string $newContent, bool $forceUpdate = false): ?array
    {
        if (empty($this->serviceUrl) || empty($this->serviceKey)) {
            Log::warning('ChatbotService: Cannot train content. Service URL or Key is missing.');
            return ['error' => 'Chatbot service not configured.'];
        }

        $currentSetting = ChatbotSetting::where('key', 'subject_content')->first();
        $currentContent = $currentSetting->value ?? '';

        // Chỉ gửi cập nhật nếu nội dung thay đổi hoặc được buộc cập nhật
        if ($newContent === $currentContent && !$forceUpdate) {
            Log::info('ChatbotService: Subject content has not changed. Skipping training.');
            return ['status' => 'skipped', 'message' => 'Content not changed.'];
        }

        $endpoint = rtrim($this->serviceUrl, '/') . '/save-knowledge'; // Đảm bảo đường dẫn đúng

        try {
            $response = Http::withHeaders([
                'SERVICE-KEY' => $this->serviceKey,
            ])->post($endpoint, [
                'content' => $newContent,
            ]);

            if ($response->successful()) {
                // Cập nhật giá trị trong database sau khi gửi thành công
                if ($currentSetting) {
                    $currentSetting->update(['value' => $newContent]);
                } else {
                    ChatbotSetting::create([
                        'key' => 'subject_content',
                        'value' => $newContent
                    ]);
                }
                Log::info('ChatbotService: Training content sent and saved successfully.', ['response' => $response->json()]);
                return $response->json();
            } else {
                Log::error('ChatbotService: Failed to train content.', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Failed to train chatbot service.', 'details' => $response->body()];
            }
        } catch (\Exception $e) {
            Log::error('ChatbotService: Exception when training content: ' . $e->getMessage());
            return ['error' => 'An error occurred while training content.', 'details' => $e->getMessage()];
        }
    }

    /**
     * Kiểm tra và xử lý logic tăng quota.
     *
     * @param string $message
     * @return bool
     */
    private function shouldIncreaseQuota(string $message): bool
    {
        $normalizedMessage = trim(mb_strtolower($message));
        return in_array($normalizedMessage, ['ok', 'có']); // Bạn có thể thêm các từ khóa khác
    }

    /**
     * Lấy key cache cho quota dựa trên IP hoặc User ID.
     *
     * @param string $ip
     * @param int|null $userId
     * @return string
     */
    private function getQuotaCacheKey(string $ip, ?int $userId): string
    {
        return $userId ? "chatbot_quota_user_{$userId}" : "chatbot_quota_ip_{$ip}";
    }

    /**
     * Quản lý quota và trả về thông báo nếu hết lượt.
     *
     * @param string $message
     * @param string $ip
     * @param int|null $userId
     * @return array|null Trả về mảng thông báo SSE nếu xử lý quota, null nếu tiếp tục.
     */
    public function handleQuota(string $message, string $ip, ?int $userId): ?array
    {
        $cacheKey = $this->getQuotaCacheKey($ip, $userId);
        $quota = Cache::get($cacheKey, $this->userQuotaLimit); // Sử dụng userQuotaLimit

        if ($this->shouldIncreaseQuota($message) && $quota <= 0) {
            // Tăng quota
            Cache::put($cacheKey, $this->userQuotaLimit, now()->endOfDay());
            Log::info("ChatbotService: Quota increased for {$cacheKey}. New quota: {$this->userQuotaLimit}");
            return [
                "retry: 1000\n",
                "data: <span class='text-success'>Bạn đã được cộng thêm {$this->userQuotaLimit} lượt gửi trong ngày</span>.\n\n",
                "data:  Hãy tiếp tục đặt câu hỏi.\n\n",
                "data: [DONE]\n\n"
            ];
        }

        if ($quota <= 0) {
            // Hết quota và không phải tin nhắn tăng lượt
            Log::info("ChatbotService: Quota exhausted for {$cacheKey}.");
            return [
                "retry: 1000\n",
                "data: <span class='text-danger'>Bạn đã hết lượt gửi trong ngày</span>.\n\n",
                "data:  Hãy gửi <b>'OK'</b> nếu muốn tăng thêm {$this->userQuotaLimit} lượt.\n\n",
                "data: [DONE]\n\n"
            ];
        }

        // Trừ quota nếu chưa hết
        Cache::put($cacheKey, $quota - 1, now()->endOfDay());
        Log::info("ChatbotService: Quota decreased for {$cacheKey}. Remaining: " . ($quota - 1));
        return null; // Tiếp tục xử lý tin nhắn
    }

    /**
     * Gửi yêu cầu đến dịch vụ API thứ ba và xử lý phản hồi là một chuỗi đơn thuần.
     *
     * @param string $userMessage Câu hỏi của người dùng.
     * @param string $model Mô hình AI (mặc định 'default').
     * @param string $type Loại cuộc hội thoại (mặc định 'text').
     * @return \Generator
     */
    public function streamChatResponse(string $userMessage, string $model = 'gpt-4o-mini', string $type = 'train'): \Generator
    {
        if (empty($this->serviceUrl) || empty($this->serviceKey)) {
            yield "data: <span class='text-danger'>Lỗi: Dịch vụ chatbot chưa được cấu hình.</span>\n\n";
            yield "data: [DONE]\n\n";
            return;
        }

        $endpoint = rtrim($this->serviceUrl, '/') . '/chat';

        try {
            // Gửi POST request như bình thường, không yêu cầu stream response
            $response = Http::timeout(120)->withHeaders([
                'SERVICE-KEY' => $this->serviceKey,
            ])->post($endpoint, [
                'question' => $userMessage,
                'type' => 'train',
            ]);

            if ($response->successful()) {
                $rawBotResponse = $response->body();
                Log::debug('ChatbotService: Raw response from API: ' . $rawBotResponse); // Debug raw response

                // Thử giải mã JSON. Nếu dịch vụ trả về một chuỗi đã json_encode,
                // json_decode sẽ trả về chuỗi gốc (ví dụ: "Đây là tiếng Việt").
                // Nếu nó không phải JSON hợp lệ, json_decode sẽ trả về null.
                $decodedResponse = json_decode($rawBotResponse);

                ChatbotMessage::create([
                    'user_id' => Auth::id(),
                    'ip_address' => request()->ip(),
                    'content' => $decodedResponse,
                    'type' => 'bot',
                    'status' => 'sent',
                ]);

                // Kiểm tra nếu giải mã thành công và kết quả là một chuỗi
                if (json_last_error() === JSON_ERROR_NONE && is_string($decodedResponse)) {
                    $fullBotResponse = $decodedResponse;
                    Log::debug('ChatbotService: Decoded response: ' . $fullBotResponse); // Debug decoded response
                } else {
                    // Nếu không phải JSON hợp lệ hoặc không phải chuỗi, sử dụng raw response
                    // và Log cảnh báo. Có thể cần kiểm tra thêm dạng dữ liệu.
                    Log::warning('ChatbotService: API response is not a valid JSON string or cannot be decoded. Using raw response.', ['raw_response' => $rawBotResponse, 'json_error' => json_last_error_msg()]);
                    $fullBotResponse = $rawBotResponse;
                }

                // Bây giờ, chúng ta sẽ "stream" chuỗi này ra từng ký tự hoặc từng đoạn nhỏ
                // để giả lập hiệu ứng stream cho người dùng cuối.
                // Điều này giúp giữ cho kết nối mở và gửi dữ liệu từ từ.

                $length = mb_strlen($fullBotResponse);
                $chunkSize = 1; // Kích thước của mỗi "chunk" (ví dụ: 1 ký tự một lần)

                for ($i = 0; $i < $length; $i += $chunkSize) {
                    $chunk = mb_substr($fullBotResponse, $i, $chunkSize);
                    yield "data: " . $chunk . "\n\n";
                    // Thêm delay nhỏ để kiểm soát tốc độ hiển thị nếu cần
                    usleep(20000); // 5ms delay per character
                }

                Log::info('ChatbotService: Full response received and simulated stream.', ['response_length' => $length]);
            } else {
                Log::error('ChatbotService: Failed to get response from chat service.', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                yield "data: <span class='text-danger'>Lỗi từ dịch vụ chatbot: " . $response->status() . " - " . $response->body() . "</span>\n\n";
            }
        } catch (\Throwable $th) {
            Log::error('ChatbotService: Exception during chat call: ' . $th->getMessage());
            yield "data: <span class='text-danger'>Lỗi trong quá trình trò chuyện: " . $th->getMessage() . "</span>\n\n";
        }
        yield "data: [DONE]\n\n"; // Đảm bảo luôn kết thúc với [DONE]
    }
}
