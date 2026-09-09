<?php

use Krbaidik\AdBsConverter\Facades\NepaliDate;


if (! function_exists('adToBs')) {
    function adToBs($date)
    {
        $explodeDate = explode('-', $date);

        $nepDate = NepaliDate::engToNep(
            $explodeDate[0],
            $explodeDate[1],
            $explodeDate[2]
        );

        return
            $nepDate['year'] . '-' .
            str_pad($nepDate['month'], 2, '0', STR_PAD_LEFT) . '-' .
            str_pad($nepDate['date'], 2, '0', STR_PAD_LEFT);
    }
}
