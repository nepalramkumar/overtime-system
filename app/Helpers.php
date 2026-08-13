<?php

use Nilambar\NepaliDate\NepaliDate;

if (!function_exists('adToBs')) {
    /**
     * AD date (YYYY-MM-DD) लाई BS मा बदल्ने, "YYYY-MM-DD" फर्काउँछ (BS मा)
     */
    function adToBs($date) {
        if (empty($date)) {
            return '';
        }

        $dateParts = explode('-', $date);
        $y = (int) $dateParts[0];
        $m = (int) $dateParts[1];
        $d = (int) $dateParts[2];

        $converter = new NepaliDate();
        $bs = $converter->convertAdToBs($y, $m, $d);

        // Package ले array फर्काउँछ: ['year' => ..., 'month' => ..., 'day' => ...]
        return sprintf('%04d-%02d-%02d', $bs['year'], $bs['month'], $bs['day']);
    }
}