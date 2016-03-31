<?php

namespace Myracloud\API\Util;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Normalizer
 */
abstract class Normalizer
{
    /**
     * Normalizes the given fqdn.
     *
     * @return \Closure
     */
    public static function normalizeFqdn()
    {
        return function (OptionsResolver $resolver, $value) {
            return trim($value, '.');
        };
    }

    /**
     * Normalizes the given date.
     *
     * @param bool $toGermanTimeZone
     * @return \Closure
     */
    public static function normalizeDate($toGermanTimeZone = true)
    {
        return function (OptionsResolver $resolver, $value) use ($toGermanTimeZone) {
            if ($value === null || empty($value)) {
                return null;
            } else if (ctype_digit($value)) { // unix timestamp
                $value = \DateTime::createFromFormat('U', $value);
            } else if (!$value instanceof \DateTime) { // string
                $value = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
            }


            if ($toGermanTimeZone) {
                $value = $value->setTimezone(new \DateTimeZone('Europe/Berlin'));
            }

            return $value;
        };
    }

    /**
     * Normalize the given input to a int value.
     *
     * @return \Closure
     */
    public static function normalizeInt()
    {
        return function (OptionsResolver $resolver, $value) {
            return (int)$value;
        };
    }

}
