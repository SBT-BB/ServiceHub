<?php

namespace App\Services;

use App\Models\OtpVerification;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Generate and store OTP for a mobile number.
     *
     * @param string $mobile
     * @return OtpVerification
     * @throws \Exception
     */
    public function generateOtp(string $mobile): OtpVerification
    {
        // Check for existing unexpired OTP within rate limit (e.g., 30 seconds)
        $lastOtp = OtpVerification::where('mobile', $mobile)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastOtp && $lastOtp->created_at->addSeconds(30)->isFuture()) {
            throw new \Exception('Please wait 30 seconds before requesting a new OTP.');
        }

        // Invalidate previous OTPs
        OtpVerification::where('mobile', $mobile)->update(['expires_at' => now()]);

        // Generate 6 digit OTP
        $otpCode = str_pad((string)rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        $otp = OtpVerification::create([
            'mobile' => $mobile,
            'otp' => $otpCode,
            'expires_at' => now()->addMinutes(5), // 5 minutes expiry
        ]);

        $this->sendOtpSms($mobile, $otpCode);

        return $otp;
    }

    /**
     * Verify the OTP.
     *
     * @param string $mobile
     * @param string $otpCode
     * @return bool
     * @throws \Exception
     */
    public function verifyOtp(string $mobile, string $otpCode): bool
    {
        $otpRecord = OtpVerification::where('mobile', $mobile)
            ->where('otp', $otpCode)
            ->where('expires_at', '>', now())
            ->whereNull('verified_at')
            ->first();

        if (!$otpRecord) {
            throw new \Exception('Invalid or expired OTP.');
        }

        $otpRecord->update(['verified_at' => now()]);

        return true;
    }

    /**
     * Mock sending OTP via SMS.
     *
     * @param string $mobile
     * @param string $otpCode
     * @return void
     */
    protected function sendOtpSms(string $mobile, string $otpCode): void
    {
        // TODO: Integrate actual SMS provider (Twilio, MSG91, etc.)
        Log::info("OTP for {$mobile} is {$otpCode}");
    }
}
