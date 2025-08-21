<?php

namespace Umii\LoginAlert\Services;

class DeviceFingerprintService
{
    public function fingerprint(string $ip, string $userAgent): string
    {
        return hash('sha256', $ip . '|' . $userAgent);
    }

    public function describe(string $userAgent): string
    {
        // Very simple UA parsing
        $ua = strtolower($userAgent);

        $os = 'Unknown OS';
        if (str_contains($ua, 'windows')) $os = 'Windows';
        elseif (str_contains($ua, 'mac os') || str_contains($ua, 'macintosh')) $os = 'macOS';
        elseif (str_contains($ua, 'linux')) $os = 'Linux';
        elseif (str_contains($ua, 'android')) $os = 'Android';
        elseif (str_contains($ua, 'iphone') || str_contains($ua, 'ios')) $os = 'iOS';

        $browser = 'Unknown Browser';
        if (str_contains($ua, 'edg')) $browser = 'Edge';
        elseif (str_contains($ua, 'chrome')) $browser = 'Chrome';
        elseif (str_contains($ua, 'firefox')) $browser = 'Firefox';
        elseif (str_contains($ua, 'safari') && !str_contains($ua, 'chrome')) $browser = 'Safari';

        return "{$os} - {$browser}";
    }
}
