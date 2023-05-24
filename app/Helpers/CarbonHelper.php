<?php

class CarbonHelper
{
    function DateTimeFormatterToStringBasedFormat(String $dateToFormat)
    {
        return Carbon\Carbon::parse($dateToFormat)->format('l, M d, Y g:i:s a');
    }
}
