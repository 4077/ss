<?php namespace ss\support;

class Support
{
    public static function parseInteger($value)
    {
        $value = round($value);

        return $value;
    }

    public static function parseDecimal($value, $decimals = null)
    {
        $value = str_replace([' ', '&nbsp;'], '', $value);
//        $value = (float)str_replace(',', '.', $value);
        $value = str_replace(',', '.', $value);

        if (is_integer($decimals)) {
            $value = number_format($value, $decimals, '.', '');
        }

        return $value;
    }

    public static function trimZeros($value)
    {
        return strpos($value, '.') ? rtrim(rtrim(rtrim($value, '0'), '.'), ',') : $value;
    }

    public static function getDayName($date)
    {
        $names = [
            'Воскресенье',
            'Понедельник',
            'Вторник',
            'Среда',
            'Четверг',
            'Пятница',
            'Суббота',
        ];

        $carbon = \Carbon\Carbon::parse($date);

        return $names[$carbon->format('w')];
    }

    public static function formatPhone($phone, $lead = '+7')
    {
        $formattedPhone = static::phoneFormat(substr($phone, -10), $lead . ' (###) ###-##-##');

        if (substr($formattedPhone, 0, strlen($lead)) != $lead) {
            $formattedPhone = $lead . substr($formattedPhone, strlen($lead));
        }

        return $formattedPhone;
    }

    public static function integerPhone($phone, $lead = 7)
    {
        $integerPhone = preg_replace('/\D/', '', str_replace('+7', $lead, $phone));

        if (substr($integerPhone, 0, 1) != $lead) {
            $integerPhone = $lead . substr($integerPhone, 1);
        }

        return $integerPhone;
    }

    /**
     * https://blog.dotzero.ru/php-phone-format/
     *
     * @param        $phone
     * @param        $format
     * @param string $mask
     *
     * @return bool|string
     */
    private function phoneFormat($phone, $format, $mask = '#')
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (is_array($format)) {
            if (array_key_exists(strlen($phone), $format)) {
                $format = $format[strlen($phone)];
            } else {
                return false;
            }
        }

        $pattern = '/' . str_repeat('([0-9])?', substr_count($format, $mask)) . '(.*)/';

        $format = preg_replace_callback(
            str_replace('#', $mask, '/([#])/'),
            function () use (&$counter) {
                return '${' . (++$counter) . '}';
            },
            $format
        );

        return ($phone) ? trim(preg_replace($pattern, $format, $phone, 1)) : false;
    }
}