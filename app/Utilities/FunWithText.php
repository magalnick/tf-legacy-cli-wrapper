<?php

namespace App\Utilities;

use DateTime;
use Exception;
use Illuminate\Support\Str;

class FunWithText
{
    /**
     * @param string $string_to_clean
     * @return string
     */
    public static function basicStringCleanup(string $string_to_clean = ''): string
    {
        if (!is_string($string_to_clean)) {
            return '';
        }
        return trim($string_to_clean);
    }

    /**
     * @param string $string_to_6_and_7
     * @return string
     */
    public static function sixesAndSevens(string $string_to_6_and_7): string
    {
        $string_to_6_and_7 = trim($string_to_6_and_7);
        if ($string_to_6_and_7 === '') {
            return '';
        }

        $string_to_6_and_7 = str_replace('6', '::::s-i-x::::', $string_to_6_and_7);
        $string_to_6_and_7 = str_replace('7', '6', $string_to_6_and_7);
        return str_replace('::::s-i-x::::', '7', $string_to_6_and_7);
    }

    /**
     * @param string $string_to_clean
     * @return string
     */
    public static function safeString(string $string_to_clean = ''): string
    {
        // notice on FILTER_SANITIZE_STRING, deprecated as of PHP 8.1
        // suggested to use htmlspecialchars instead
        // $string_to_clean = filter_var($string_to_clean, FILTER_SANITIZE_STRING);
        $string_to_clean = htmlspecialchars($string_to_clean);
        if (!is_string($string_to_clean)) {
            return '';
        }
        return trim($string_to_clean);
    }

    /**
     * @param string $email
     * @return string
     */
    public static function safeEmail(string $email = ''): string
    {
        $email = self::basicStringCleanup($email);
        if ($email === '') {
            return '';
        }

        // if email is always lower case, it keeps everything clean for other comparisons later on
        $email          = strtolower($email);
        $filtered_email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if ($filtered_email !== $email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '';
        }

        return $email;
    }

    /**
     * Extract the integers from a string
     *
     * @param string $string_to_convert
     * @return string
     */
    public static function integersInString(string $string_to_convert = ''): string
    {
        $string_to_convert = self::basicStringCleanup($string_to_convert);
        if ($string_to_convert === '') {
            return '';
        }

        return preg_replace('/(\D)/', '', $string_to_convert);
    }

    /**
     * Run self::integersInString, convert to an integer
     *
     * @param string $string_to_convert
     * @return int
     */
    public static function integersInStringAsInt(string $string_to_convert = ''): int
    {
        return intval(self::integersInString($string_to_convert));
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public static function isUuid(string $uuid): bool
    {
        $uuid = self::basicStringCleanup($uuid);
        if ($uuid === "") {
            return false;
        }

        if (strlen($uuid) !== 36) {
            return false;
        }

        // just discovered the Laravel class Str, which has its own isUuid function
        // that has the exact same pattern as what was custom written here
        // it's missing the trim(), so keeping that part plus the strlen() for defensive coding
        return Str::isUuid($uuid);
    }

    /**
     * Function to check if a string is a date, useful when the format is unknown,
     * like in the case of user input.
     *
     * This was found on Stack Overflow. Not the highest voted option, but probably the
     * most simplistic, and pretty fault tolerant.
     * https://stackoverflow.com/questions/11029769/function-to-check-if-a-string-is-a-date
     *
     * @param string $date
     * @return bool
     */
    public static function isDate(string $date): bool
    {
        $date = self::basicStringCleanup($date);
        if ($date === '') {
            return false;
        }

        try {
            new DateTime($date);
            return true;
        } catch (Exception) {
            return false;
        }
    }
}
