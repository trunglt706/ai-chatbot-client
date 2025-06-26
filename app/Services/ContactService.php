<?php

namespace App\Services;

use App\Models\RequestForm;
use App\Notifications\NewRequestFormNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class ContactService
{
    // Định nghĩa các loại yêu cầu
    public const TYPE_PROJECT_COLLABORATION = 1;
    public const TYPE_MODULE_SPONSORSHIP = 2;
    public const TYPE_MODULE_DEMO = 3;
    public const TYPE_OTHER = 4;

    // Định nghĩa trạng thái
    public const STATUS_NEW = 1;
    public const STATUS_IN_PROGRESS = 2;
    public const STATUS_COMPLETED = 3;
    public const STATUS_CANCELED = 4;

    public static function getFormTypes(): array
    {
        return [
            self::TYPE_PROJECT_COLLABORATION => 'Đăng ký hợp tác dự án',
            self::TYPE_MODULE_SPONSORSHIP => 'Đăng ký nhà tài trợ module',
            self::TYPE_MODULE_DEMO => 'Đăng ký demo module',
            self::TYPE_OTHER => 'Khác',
        ];
    }

    public static function getStatus(): array
    {
        return [
            self::STATUS_NEW => 'Mới',
            self::STATUS_IN_PROGRESS => 'Đang xử lý',
            self::STATUS_COMPLETED => 'Đã hoàn thành',
            self::STATUS_CANCELED => 'Đã hủy',
        ];
    }

    /**
     * Tạo mới một liên hệ.
     *
     * @param array $data Dữ liệu yêu cầu từ form.
     * @param \App\Models\User|null $user Người dùng đã đăng nhập (nếu có).
     * @return \App\Models\RequestForm
     */
    public function createContact(array $data, ?\App\Models\User $user = null): RequestForm
    {
        $contact = RequestForm::create([
            'user_id' => $user ? $user->id : null,
            'type' => $data['type'],
            'module_id' => $data['module_id'],
            'content' => $data['content'],
            'status' => self::STATUS_NEW,
        ]);

        $this->sendSlackNotification($contact);
        $this->logActivity($contact, 'New contact submitted');

        return $contact;
    }

    /**
     * Cập nhật một liên hệ hiện có.
     *
     * @param \App\Models\RequestForm $contact
     * @param array $data Dữ liệu cập nhật từ form.
     * @param \App\Models\User|null $user Người dùng thực hiện cập nhật.
     * @return \App\Models\RequestForm
     */
    public function updateContact(RequestForm $contact, array $data, ?\App\Models\User $user = null): RequestForm
    {
        $oldContact = clone $contact;

        $contact->fill([
            'type' => $data['type'],
            'module_id' => $data['module_id'],
            'content' => $data['content'],
            'status' => $data['status'] ?? $contact->status,
        ]);
        $contact->save();

        $this->logActivity($contact, 'Contact updated', $oldContact);

        return $contact;
    }

    /**
     * Gửi thông báo Slack.
     *
     * @param \App\Models\RequestForm $contact
     * @return void
     */
    protected function sendSlackNotification(RequestForm $contact): void
    {
        Notification::route('slack', config('services.slack.webhook_url'))
            ->notify(new NewRequestFormNotification($contact));
    }

    /**
     * Ghi log hoạt động.
     *
     * @param \App\Models\RequestForm $contact
     * @param string $description
     * @param \App\Models\RequestForm|null $oldContact (chỉ cho cập nhật)
     * @return void
     */
    protected function logActivity(RequestForm $contact, string $description, ?RequestForm $oldContact = null): void
    {
        $activity = activity()
            ->performedOn($contact)
            ->causedBy(Auth::user() ?: null)
            ->withProperties([
                'contact_type' => $contact->type,
                'status' => $contact->status,
            ]);

        if ($oldContact) {
            $activity->withProperty('old_attributes', $oldContact->getOriginal());
            $activity->withProperty('new_attributes', $contact->getAttributes());
        }

        $activity->log($description);
    }
}
