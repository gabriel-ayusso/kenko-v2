<?php

namespace App\Helpers;

class Helper
{
    public static function bookingStatus($status)
    {
        switch ($status) {
            case 'A':
                return 'Agendado';
            case 'E':
                return 'Executado';
            case 'P':
                return 'Pago';
            case 'C':
                return 'Cancelado';
            default:
                return '--';
        }
    }

    public static function removeFormats($str)
    {
        return preg_replace('/[^[:alnum:]]*/', '', $str);
    }
}
