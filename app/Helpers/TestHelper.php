<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Notifications\TestSlackNotification;
use App\Events\TestBroadcastEvent;

if (!function_exists('check_broadcast_config')) {
    /**
     * Kiểm tra cấu hình Broadcasting.
     *
     * @return array
     */
    function check_broadcast_config(): array
    {
        $driver = Config::get('broadcasting.default');
        $status = [
            'status' => 'inactive',
            'message' => 'Broadcasting driver is not configured or is set to "log" or "null".',
            'driver' => $driver,
            'config' => []
        ];

        if (in_array($driver, ['pusher', 'redis', 'ably', 'log', 'null'])) {
            $configured = true;
            switch ($driver) {
                case 'pusher':
                    $status['config'] = [
                        'Key' => Config::get('broadcasting.connections.pusher.key') ? 'Configured' : 'Missing',
                        'Secret' => Config::get('broadcasting.connections.pusher.secret') ? 'Configured' : 'Missing',
                        'App ID' => Config::get('broadcasting.connections.pusher.app_id') ? 'Configured' : 'Missing',
                        'Cluster' => Config::get('broadcasting.connections.pusher.options.cluster') ? 'Configured' : 'Missing',
                    ];
                    if ($status['config']['Key'] === 'Missing' || $status['config']['Secret'] === 'Missing' || $status['config']['App ID'] === 'Missing' || $status['config']['Cluster'] === 'Missing') {
                        $configured = false;
                    }
                    break;
                case 'redis':
                    $status['config'] = [
                        'Host' => Config::get('database.redis.default.host') ? 'Configured' : 'Missing',
                        'Port' => Config::get('database.redis.default.port') ? 'Configured' : 'Missing',
                    ];
                    if ($status['config']['Host'] === 'Missing' || $status['config']['Port'] === 'Missing') {
                        $configured = false;
                    }
                    break;
                case 'ably':
                    $status['config'] = [
                        'Key' => Config::get('broadcasting.connections.ably.key') ? 'Configured' : 'Missing',
                    ];
                    if ($status['config']['Key'] === 'Missing') {
                        $configured = false;
                    }
                    break;
                case 'log':
                    $status['status'] = 'active'; // Log driver is always considered 'active' for configuration check
                    $status['message'] = 'Broadcasting (Log driver) is configured. Events will be logged.';
                    break;
                case 'null':
                    $status['status'] = 'inactive';
                    $status['message'] = 'Broadcasting (Null driver) is configured. Events will be discarded.';
                    break;
                default:
                    $status['config'] = ['Note' => 'Check your specific driver configuration.'];
                    break;
            }
            if ($configured && !in_array($driver, ['log', 'null'])) { // Exclude log/null from needing specific config checks
                $status['status'] = 'active';
                $status['message'] = 'Broadcasting (' . ucfirst($driver) . ') is configured.';
            } else if ($configured && $driver === 'log') {
                // Already handled above
            } else if ($configured && $driver === 'null') {
                // Already handled above
            } else {
                $status['message'] = 'Broadcasting (' . ucfirst($driver) . ') is configured but has missing credentials in .env.';
            }
        }
        return $status;
    }
}

if (!function_exists('check_email_config')) {
    /**
     * Kiểm tra cấu hình Email.
     *
     * @return array
     */
    function check_email_config(): array
    {
        $mailer = Config::get('mail.mailer');
        $host = Config::get('mail.mailers.smtp.host');
        $port = Config::get('mail.mailers.smtp.port');
        $username = Config::get('mail.mailers.smtp.username');
        $password = Config::get('mail.mailers.smtp.password');
        $encryption = Config::get('mail.mailers.smtp.encryption');
        $fromAddress = Config::get('mail.from.address');
        $fromName = Config::get('mail.from.name');

        $status = [
            'status' => 'inactive',
            'message' => 'Email driver is not configured or is set to "log" or "array".',
            'driver' => $mailer,
            'config' => [
                'Host' => $host ? 'Configured' : 'Missing',
                'Port' => $port ? 'Configured' : 'Missing',
                'Username' => $username ? 'Configured' : 'Missing',
                'Password' => $password ? 'Configured' : 'Missing',
                'Encryption' => $encryption ?? 'N/A',
                'From Address' => $fromAddress ? 'Configured' : 'Missing',
                'From Name' => $fromName ? 'Configured' : 'Missing',
            ]
        ];

        if ($mailer === 'smtp') {
            if (!empty($host) && !empty($port) && !empty($username) && !empty($password) && !empty($fromAddress)) {
                $status['status'] = 'active';
                $status['message'] = 'Email (SMTP) driver is fully configured.';
            } else {
                $status['message'] = 'Email (SMTP) driver configured but has missing critical credentials in .env.';
            }
        } elseif (!in_array($mailer, ['log', 'array'])) {
            if (!empty($fromAddress)) {
                $status['status'] = 'active';
                $status['message'] = 'Email driver is configured for: ' . $mailer;
            } else {
                $status['message'] = 'Email driver configured but "MAIL_FROM_ADDRESS" is missing in .env.';
            }
        }

        return $status;
    }
}

if (!function_exists('check_slack_config')) {
    /**
     * Kiểm tra cấu hình Slack.
     *
     * @return array
     */
    function check_slack_config(): array
    {
        $webhookUrl = Config::get('logging.channels.slack.url');
        $token = Config::get('services.slack.token');

        $status = [
            'status' => 'inactive',
            'message' => 'Slack webhook URL or app token is not configured in .env or services.php.',
            'config' => [
                'Webhook URL (Logging)' => $webhookUrl ? 'Configured' : 'Missing',
                'App Token (Services)' => $token ? 'Configured' : 'Missing',
            ]
        ];

        if (!empty($webhookUrl) || !empty($token)) {
            $status['status'] = 'active';
            $status['message'] = 'Slack configuration detected.';
        }

        return $status;
    }
}

// Helper class ảo để nhận notification nếu không muốn dùng User Model thật
if (!class_exists('NotifiableDummy')) {
    class NotifiableDummy
    {
        use \Illuminate\Notifications\Notifiable;
        public $id = 0; // Cần có ID nếu notifiable routing dựa vào đó

        public function routeNotificationForSlack($notification)
        {
            return config('logging.channels.slack.url') ?? 'https://hooks.slack.com/services/YOUR/WEBHOOK/URL';
        }
    }
}

if (!function_exists('test_broadcast_service')) {
    /**
     * Thực hiện kiểm tra gửi sự kiện Broadcasting.
     *
     * @return array
     */
    function test_broadcast_service(): array
    {
        $driver = Config::get('broadcasting.default');
        $result = [
            'status' => 'error',
            'message' => 'Broadcasting test failed or driver is not set for live testing.',
        ];

        if (in_array($driver, ['pusher', 'redis', 'ably'])) {
            try {
                // Giả định bạn đã tạo event TestBroadcastEvent
                event(new TestBroadcastEvent('Test message from admin panel.'));
                $result['status'] = 'success';
                $result['message'] = 'Broadcast event dispatched successfully (via ' . ucfirst($driver) . '). Check your broadcasting logs/dashboard for real-time verification.';
            } catch (Exception $e) {
                $result['message'] = 'Failed to dispatch broadcast event: ' . $e->getMessage();
                Log::error('Broadcast test failed: ' . $e->getMessage());
            }
        } else {
            $result['message'] = 'Broadcasting driver is set to "' . $driver . '". Live testing is not applicable or requires specific setup.';
        }
        return $result;
    }
}

if (!function_exists('test_email_service')) {
    /**
     * Thực hiện kiểm tra gửi Email.
     *
     * @param string $recipient
     * @return array
     */
    function test_email_service(string $recipient): array
    {
        try {
            Mail::raw('This is a test email sent from your Laravel application\'s admin panel. If you received this, your email configuration is working!', function ($message) use ($recipient) {
                $message->to($recipient)
                    ->subject('Laravel Email Test from Admin Panel');
            });
            $result = ['status' => 'success', 'message' => 'Test email sent successfully to ' . $recipient . '. Please check your inbox.'];
        } catch (Exception $e) {
            $result = ['status' => 'error', 'message' => 'Failed to send test email: ' . $e->getMessage()];
            Log::error('Email test failed: ' . $e->getMessage());
        }
        return $result;
    }
}

if (!function_exists('test_slack_service')) {
    /**
     * Thực hiện kiểm tra gửi thông báo Slack.
     *
     * @return array
     */
    function test_slack_service(): array
    {
        try {
            $notifiable = new NotifiableDummy();
            Notification::send($notifiable, new TestSlackNotification('Test message from admin panel.'));

            $result = ['status' => 'success', 'message' => 'Test Slack notification dispatched successfully. Check your Slack channel.'];
        } catch (Exception $e) {
            $result = ['status' => 'error', 'message' => 'Failed to send Slack notification: ' . $e->getMessage()];
            Log::error('Slack test failed: ' . $e->getMessage());
        }
        return $result;
    }
}
