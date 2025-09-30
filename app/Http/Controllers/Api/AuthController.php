<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cari siapa referrer-nya
        $referrer = null;
        if ($request->referral_code) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
        }

        // Generate kode referral unik untuk user baru
        do {
            $newReferral = strtoupper(Str::random(8));
        } while (User::where('referral_code', $newReferral)->exists());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referred_by' => $referrer ? $referrer->id : null,
            'referral_code' => $newReferral,
        ]);

        // Generate OTP untuk verifikasi email
        $otp = OtpCode::create([
            'user_id' => $user->id,
            'code' => rand(100000, 999999),
            'type' => 'email',
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully. Please verify your email.',
            'user' => $user,
            'token' => $token,
            'otp_sent' => true,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
            'type' => 'required|in:email,sms',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $otp = OtpCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('type', $request->type)
            ->where('status', 'pending')
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Invalid OTP code'], 400);
        }

        if ($otp->isExpired()) {
            $otp->update(['status' => 'expired']);
            return response()->json(['message' => 'OTP code has expired'], 400);
        }

        $otp->update(['status' => 'used']);
        $user->update(['is_verified' => true]);



        return response()->json(['message' => 'OTP verified successfully']);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:email,sms',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        // Expire old OTPs
        OtpCode::where('user_id', $user->id)
            ->where('type', $request->type)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        // Generate OTP baru
        $otp = OtpCode::create([
            'user_id' => $user->id,
            'code' => random_int(100000, 999999),
            'type' => $request->type,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Kirim OTP via Email
        if ($request->type === 'email') {
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
            $apiInstance = new TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

            $email = new SendSmtpEmail([
                'subject' => 'Your OTP Code',
                'sender' => [
                    'name' => 'vega.vaul',
                    'email' => 'admin@vaultsy.online'
                ],
                'to' => [[
                    'email' => $user->email,
                    'name' => $user->name
                ]],
                'htmlContent' => "<p>Use this OTP to verify your account: <strong>{$otp->code}</strong></p>"
            ]);

            try {
                $apiInstance->sendTransacEmail($email);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to send OTP: ' . $e->getMessage()], 500);
            }
        }

        // Jika ingin tambah SMS, nanti implementasi di sini (Brevo juga support SMS)

        return response()->json(['message' => 'OTP sent successfully']);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function profile(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }
}
