<?php

namespace App\Services;

use Carbon\Carbon;
use InvalidArgumentException;

class QrCodeCredentialsService
{
    /**
     * Decode a scanned membership QR code into its component parts.
     *
     * Format: "<code>|<name>|<group1,group2,...>" where <code> packs a
     * timestamp (MM HH DD MM YY, 10 chars) followed by a family-number-length
     * digit and the encoded membership/family number.
     *
     * @return array{membership_code: string, name: string, groups: array<int, string>}
     */
    public function decode(string $qrCode): array
    {
        $parts = explode('|', $qrCode);
        $code = $parts[0] ?? '';

        if (strlen($code) < 14) {
            throw new InvalidArgumentException('Invalid QR code format.');
        }

        // Parse datetime components (freshness check intentionally disabled, mirrors the API).
        $minutes = (int) substr($code, 0, 2);
        $hours = (int) substr($code, 2, 2);
        $day = (int) substr($code, 4, 2);
        $month = (int) substr($code, 6, 2);
        $year = 2000 + (int) substr($code, 8, 2);
        Carbon::create($year, $month, $day, $hours, $minutes);

        // Parse membership components.
        $familyNumberLength = (int) substr($code, 10, 1);
        $NR = substr($code, -$familyNumberLength);
        $membershipPart = substr($code, 13, -$familyNumberLength);
        $membership_code = "E1C1F{$membershipPart}NR{$NR}";

        $name = $parts[1] ?? '';
        $group = $parts[2] ?? '';
        $groups = $group ? explode(',', $group) : [];

        return compact('membership_code', 'name', 'groups');
    }
}
