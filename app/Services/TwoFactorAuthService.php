<?php

namespace App\Services;

use Illuminate\Support\Str;

class TwoFactorAuthService
{
    /**
     * Generate a random secret key for 2FA.
     */
    public function generateSecret(): string
    {
        return $this->base32Encode(random_bytes(20));
    }

    /**
     * Generate backup recovery codes.
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = Str::upper(Str::random(4) . '-' . Str::random(4));
        }
        return $codes;
    }

    /**
     * Generate QR code URL for authenticator apps.
     */
    public function getQRCodeUrl(string $companyName, string $email, string $secret): string
    {
        $encodedCompany = rawurlencode($companyName);
        $encodedEmail = rawurlencode($email);

        return "otpauth://totp/{$encodedCompany}:{$encodedEmail}?secret={$secret}&issuer={$encodedCompany}";
    }

    /**
     * Verify a TOTP code against the secret.
     */
    public function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $timestamp = floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            if ($this->generateCode($secret, $timestamp + $i) === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a TOTP code for the given secret and timestamp.
     */
    private function generateCode(string $secret, int $timestamp): string
    {
        $secretKey = $this->base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $timestamp);
        $hash = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;

        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Base32 encode.
     */
    private function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $v = 0;
        $vbits = 0;

        for ($i = 0, $j = strlen($data); $i < $j; $i++) {
            $v = ($v << 8) | ord($data[$i]);
            $vbits += 8;

            while ($vbits >= 5) {
                $vbits -= 5;
                $output .= $alphabet[($v >> $vbits) & 0x1f];
            }
        }

        if ($vbits > 0) {
            $output .= $alphabet[($v << (5 - $vbits)) & 0x1f];
        }

        return $output;
    }

    /**
     * Base32 decode.
     */
    private function base32Decode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $v = 0;
        $vbits = 0;

        for ($i = 0, $j = strlen($data); $i < $j; $i++) {
            $v = ($v << 5) | strpos($alphabet, $data[$i]);
            $vbits += 5;

            if ($vbits >= 8) {
                $vbits -= 8;
                $output .= chr(($v >> $vbits) & 0xff);
            }
        }

        return $output;
    }
}
