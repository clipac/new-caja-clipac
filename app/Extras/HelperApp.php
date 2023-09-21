<?php

namespace App\Extras;

use Carbon\Carbon;

class HelperApp
{
    public static function getDiffDates(Carbon $startDate, Carbon $endDate): string
    {
        $diff = $startDate->diffInYears($endDate);
        if ($diff > 0) return $diff . ' años';

        $diff = $startDate->diffInMonths($endDate);
        if ($diff > 0) return $diff . ' meses';

        $diff = $startDate->diffInDays($endDate);
        if ($diff > 1) return $diff . ' días';
        if ($diff == 1) return $diff . ' día';
        if ($diff <= 0) return '< 1 día';
    }
}
