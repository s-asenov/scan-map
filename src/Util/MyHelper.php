<?php

namespace App\Util;

use DateTimeInterface;
use Exception;

/**
 * Class MyHelper
 *
 * The class will contain helpful used throughout the application
 *
 * @package App\Util
 */
class MyHelper
{
    /**
     * Formats the given date time to the corresponding format.
     *
     * @param DateTimeInterface $date
     * @return string|false
     */
    public function formatDate(DateTimeInterface $date): string|false
    {
        return date_format($date, 'Y-m-d H:i:s');
    }

    /**
     * The method is used to generate random strings.
     *
     * @param int $length
     * @param string $keyspace
     * @return string
     * @throws Exception
     */
    public function randomStr(int $length = 20, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
}

