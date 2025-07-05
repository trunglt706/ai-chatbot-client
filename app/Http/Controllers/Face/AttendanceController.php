<?php

namespace App\Http\Controllers\Face;

use App\Http\Controllers\Controller;
use App\Models\Face\FaceAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AttendanceController extends Controller
{
    protected $pythonMlServiceUrl;

    public function __construct()
    {
        $this->pythonMlServiceUrl = env('PYTHON_ML_SERVICE_URL', 'http://localhost:5000/api');
    }

    // API để chấm công bằng nhận diện khuôn mặt
    public function checkIn(Request $request)
    {
        $request->validate([
            'face_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'client_age' => 'nullable|integer',
            'client_gender' => 'nullable|string',
            'client_expression' => 'nullable|string',
        ]);

        $imageFile = $request->file('face_image');

        try {
            // Gửi ảnh đến Backend Python ML để nhận diện
            $response = Http::attach(
                'face_image',
                file_get_contents($imageFile->getRealPath()), // Lấy nội dung file
                $imageFile->getClientOriginalName() // Tên file gốc
            )->post("{$this->pythonMlServiceUrl}/process-face", [
                'client_age' => $request->client_age,
                'client_gender' => $request->client_gender,
                'client_expression' => $request->client_expression,
            ]);

            if ($response->successful()) {
                $mlResult = $response->json();

                $recognizedName = $mlResult['name'] ?? null;
                $confidence = $mlResult['confidence'] ?? 0.0;
                $mlFaceId = $mlResult['face_id'] ?? null;

                $user = null;
                if ($mlFaceId) {
                    // Tìm user dựa trên face_ml_id mà ML service trả về
                    $user = User::where('face_ml_id', $mlFaceId)->first();
                } else if ($recognizedName) {
                    // Fallback: Nếu ML service chỉ trả về tên, tìm theo tên
                    $user = User::where('name', $recognizedName)->first();
                }

                if ($user && $confidence >= 0.7) { // Ngưỡng tin cậy 70%
                    // Logic chấm công: Check-in hoặc Check-out
                    $latestAttendance = $user->faceAttendances() // Sử dụng mối quan hệ mới
                        ->whereNull('check_out_time')
                        ->latest()
                        ->first();

                    $attendance = null;
                    if ($latestAttendance) { // Đã check-in, giờ là check-out
                        $latestAttendance->update(['check_out_time' => now()]);
                        $message = 'Chấm công OUT thành công!';
                        $status = 'checked_out';
                        $attendance = $latestAttendance;
                    } else { // Check-in mới
                        $attendance = FaceAttendance::create([ // Tạo bản ghi chấm công
                            'user_id' => $user->id,
                            'check_in_time' => now(),
                            'status' => 'checked_in',
                            'confidence' => $confidence,
                        ]);
                        $message = 'Chấm công IN thành công!';
                        $status = 'checked_in';
                    }

                    // *** LƯU ẢNH VÀO SPATIE MEDIALIBRARY ***
                    if ($attendance) {
                        $attendance->addMediaFromRequest('face_image') // 'face_image' là tên trường trong formData
                            ->toMediaCollection('attendance_images'); // 'attendance_images' là tên collection
                    }

                    return response()->json([
                        'message' => $message,
                        'name' => $user->name,
                        'age' => $mlResult['age'] ?? 'N/A',
                        'confidence' => $confidence,
                        'status' => $status,
                        'timestamp' => now()->toDateTimeString(),
                        'media_url' => $attendance ? $attendance->getFirstMediaUrl('attendance_images') : null // URL của ảnh đã lưu
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Không nhận diện được khuôn mặt hoặc độ tin cậy thấp.',
                        'name' => 'Không xác định',
                        'age' => 'N/A',
                        'confidence' => $confidence,
                        'status' => 'not_recognized'
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => 'Lỗi từ dịch vụ nhận diện khuôn mặt.',
                    'ml_error' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            \Log::error('Error in checkIn process: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi hệ thống khi chấm công.'], 500);
        }
    }

    // API để đăng ký khuôn mặt mới (chỉ dành cho Admin/Quản lý)
    public function registerFace(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'face_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $user = User::find($request->user_id); // Tìm user bằng ID
        $imageFile = $request->file('face_image');

        try {
            // Gửi ảnh đến Backend Python ML để huấn luyện/lưu trữ embedding
            $response = Http::attach(
                'face_image',
                file_get_contents($imageFile->getRealPath()),
                $imageFile->getClientOriginalName()
            )->post("{$this->pythonMlServiceUrl}/train-face", [
                'user_id' => $user->id, // Gửi ID của user tới ML service
                'user_name' => $user->name,
            ]);

            if ($response->successful()) {
                $mlResult = $response->json();
                $faceMlId = $mlResult['face_id'] ?? null;

                if ($faceMlId) {
                    $user->face_ml_id = $faceMlId; // Lưu ID khuôn mặt từ ML service vào DB của Laravel
                    $user->save();

                    // Tùy chọn: Lưu ảnh khuôn mặt đăng ký vào MediaLibrary của User model
                    $user->addMediaFromRequest('face_image')
                        ->toMediaCollection('profile_faces'); // Hoặc 'avatar', 'face_register_images'

                    return response()->json([
                        'message' => 'Đăng ký khuôn mặt thành công!',
                        'user_name' => $user->name,
                        'face_ml_id' => $faceMlId,
                        'profile_image_url' => $user->getFirstMediaUrl('profile_faces') // URL ảnh đã lưu
                    ], 200);
                } else {
                    return response()->json(['message' => 'ML Service không trả về Face ID.'], 500);
                }
            } else {
                return response()->json([
                    'message' => 'Lỗi từ dịch vụ ML khi đăng ký khuôn mặt.',
                    'ml_error' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            \Log::error('Error registering face: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi hệ thống khi đăng ký khuôn mặt.'], 500);
        }
    }
}
