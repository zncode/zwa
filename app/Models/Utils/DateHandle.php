<?php

namespace App\Models\Utils;

use Illuminate\Database\Eloquent\Model;

class DateHandle extends Model
{
    public static function dateFormat($formatIn, $formatOut, $str)
    {
        if($formatOut == 'timestamp')
        {
            $date = \DateTime::createFromFormat($formatIn, trim($str));
            $isoDatetime = $date->format(\Datetime::ATOM);
            $result = strtotime($isoDatetime);
        }
        else
        {
            $date = \DateTime::createFromFormat($formatIn, trim($str));
            $result = $date->format($formatOut);
        }
        return $result;
    }
}
