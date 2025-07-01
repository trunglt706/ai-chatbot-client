<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    protected $onlineThresholdMinutes;

    public function __construct()
    {
        $this->onlineThresholdMinutes = config('auth.online_threshold_minutes', 5);
    }

    /**
     * Kiểm tra xem một người dùng cụ thể có đang online hay không và trả về thời gian hoạt động gần nhất.
     * Đây là phiên bản độc lập để kiểm tra một user.
     *
     * @param \App\Models\User $user Đối tượng User cần kiểm tra.
     * @return array Trả về một mảng với cấu trúc:
     * ['is_online' => bool, 'last_activity_at' => Carbon|null]
     */
    public function isUserOnline(User $user): array
    {
        $sessionThreshold = Carbon::now()->subMinutes($this->onlineThresholdMinutes)->timestamp;

        // Query trực tiếp để lấy session gần nhất của user này
        $latestSession = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->first();

        $isOnline = false;
        $lastActivityAt = null;

        if ($latestSession) {
            $lastActivityAt = Carbon::createFromTimestamp($latestSession->last_activity);
            if ($latestSession->last_activity >= $sessionThreshold) {
                $isOnline = true;
            }
        }

        return [
            'is_online' => $isOnline,
            'last_activity_at' => $lastActivityAt
        ];
    }

    /**
     * Lấy trạng thái online và thời gian hoạt động cuối cùng cho một tập hợp người dùng.
     * Đã tối ưu để đảm bảo nhất quán với isUserOnline bằng cách sử dụng subquery cho latest_activity.
     *
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\LengthAwarePaginator $users Tập hợp các đối tượng User.
     * @return array Trả về một mảng kết hợp [user_id => ['is_online' => bool, 'last_activity_at' => Carbon|null]].
     */
    public function getOnlineStatusesForUsers(LengthAwarePaginator|Collection $users): array
    {
        $userStatusData = [];
        $sessionThreshold = Carbon::now()->subMinutes($this->onlineThresholdMinutes)->timestamp;

        $userIds = $users->pluck('id')->toArray();

        // Sử dụng subquery để lấy latest_activity cho mỗi user_id trong một truy vấn duy nhất.
        // Đây là cách hiệu quả và nhất quán hơn so với groupBy.
        $latestSessionsData = DB::table('sessions')
            ->select('user_id', DB::raw('MAX(last_activity) as latest_activity'))
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id'); // Key bởi user_id để dễ truy cập

        foreach ($users as $user) {
            $isOnline = false;
            $lastActivityAt = null;

            if (isset($latestSessionsData[$user->id])) {
                $latestTimestamp = $latestSessionsData[$user->id]->latest_activity;

                $lastActivityAt = Carbon::createFromTimestamp($latestTimestamp);
                if ($latestTimestamp >= $sessionThreshold) {
                    $isOnline = true;
                }
            }

            $userStatusData[$user->id] = [
                'is_online' => $isOnline,
                'last_activity_at' => $lastActivityAt
            ];
        }

        return $userStatusData;
    }
}
