<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the OAuth provider's authentication page.
     *
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the OAuth provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback(string $provider)
    {
        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            // Log the error for debugging
            logger()->error("Socialite callback failed for {$provider}: " . $e->getMessage());
            return redirect('/login')->with('error', 'Có lỗi xảy ra khi xác thực với ' . ucfirst($provider) . '. Vui lòng thử lại.');
        }

        // Kiểm tra xem đã có tài khoản social này trong hệ thống chưa
        $socialAccount = SocialAccount::where('provider_name', $provider)
            ->where('provider_id', $socialiteUser->getId())
            ->first();

        if ($socialAccount) {
            // Nếu tài khoản social đã tồn tại, đăng nhập người dùng
            Auth::login($socialAccount->user, true);
            return redirect()->intended('/dashboard');
        } else {
            $user = User::where('email', $socialiteUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialiteUser->getName() ?? $socialiteUser->getNickname() ?? $socialiteUser->getEmail(),
                    'email' => $socialiteUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                ]);
            }

            // Tạo hoặc liên kết tài khoản social với người dùng
            $user->socialAccounts()->create([
                'provider_name' => $provider,
                'provider_id' => $socialiteUser->getId(),
            ]);

            Auth::login($user, true);
            return redirect()->intended('/dashboard');
        }
    }

    /**
     * Handles the Facebook Data Deletion Callback.
     * This endpoint receives a POST request from Facebook when a user
     * requests their data to be deleted from your application.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataDeletionCallbackFacebook(Request $request)
    {
        // Log the incoming request for debugging purposes
        Log::info('Facebook Data Deletion Callback received.', $request->all());

        // Ensure the 'signed_request' is present in the POST data
        if (!$request->has('signed_request')) {
            Log::error('Facebook Data Deletion: Missing signed_request.');
            return response()->json(['error' => 'Missing signed_request'], 400);
        }

        $signedRequest = $request->input('signed_request');
        $facebookAppSecret = env('FACEBOOK_CLIENT_SECRET');

        // Validate that the app secret is set
        if (empty($facebookAppSecret)) {
            Log::critical('Facebook App Secret is not set in .env. Cannot validate signed_request.');
            return response()->json(['error' => 'Server configuration error: Facebook App Secret missing.'], 500);
        }

        // Parse and validate the signed request
        $data = $this->parseSignedRequest($signedRequest, $facebookAppSecret);

        if (is_null($data)) {
            Log::error('Facebook Data Deletion: Invalid signed_request signature or payload.');
            return response()->json(['error' => 'Invalid signed_request'], 403);
        }

        // Extract user ID from the decoded data
        $facebookUserId = $data['user_id'] ?? null;

        if (empty($facebookUserId)) {
            Log::error('Facebook Data Deletion: User ID not found in signed_request.', ['data' => $data]);
            return response()->json(['error' => 'User ID not found in signed_request'], 400);
        }

        SocialAccount::where(['provider_name' => 'facebook', 'provider_id' => $facebookUserId])->delete();

        // For demonstration, we'll generate a dummy confirmation code and status URL.
        $confirmationCode = Str::uuid()->toString(); // Generate a unique ID for this deletion request
        $statusUrl = url('/account-deletion-status/' . $confirmationCode); // URL where user can check status

        // Respond to Facebook with the deletion status URL and confirmation code
        $responsePayload = [
            'url' => $statusUrl,
            'confirmation_code' => $confirmationCode,
        ];

        Log::info('Facebook Data Deletion Callback successful response sent.', $responsePayload);

        return response()->json($responsePayload, 200);
    }

    /**
     * Parses the Facebook signed_request.
     *
     * @param string $signedRequest The signed_request string from Facebook.
     * @param string $secret        Your Facebook App Secret.
     * @return array|null           Decoded data or null if invalid.
     */
    private function parseSignedRequest(string $signedRequest, string $secret): ?array
    {
        list($encodedSig, $payload) = explode('.', $signedRequest, 2);

        // Decode the data
        $sig = $this->base64UrlDecode($encodedSig);
        $data = json_decode($this->base64UrlDecode($payload), true);

        // Confirm the signature
        $expectedSig = hash_hmac('sha256', $payload, $secret, $raw = true);

        if ($sig !== $expectedSig) {
            return null;
        }

        return $data;
    }

    /**
     * Decodes a base64url encoded string.
     *
     * @param string $input The base64url encoded string.
     * @return string       The decoded string.
     */
    private function base64UrlDecode(string $input): string
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
