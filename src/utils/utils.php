<?php

class NotImplementedException extends BadMethodCallException
{
}

function areValuesSame($value, $value2)
{
    return $value === $value2;
}

function isEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isTwoOrMoreWords($string)
{
    $words = preg_split('/\s+/', trim($string));
    return count($words) >= 2;
}

function isEmpty($var)
{
    if (is_string($var)) {
        return trim($var) === '';
    }
    if (is_array($var)) {
        return empty($var);
    }
}

function isNotEmpty($var)
{
    return !isEmpty($var);
}

function isPhoneNumber($value)
{
    $pattern = '/^\d{9}$/';
    return preg_match($pattern, $value);
}

function isNumber($value)
{
    return is_numeric($value);
}

function isInteger($value)
{
    return is_int($value);
}

function inRange($value, $min = null, $max = null): bool
{
    if ($min !== null && $max !== null) {
        return $value >= $min && $value <= $max;
    } elseif ($min !== null) {
        return $value >= $min;
    } elseif ($max !== null) {
        return $value <= $max;
    } else {
        return true;
    }
}

function isPasswordStrong($password)
{
    $minLength = 8;
    $hasUpperCase = preg_match('/[A-Z]/', $password);
    $hasLowerCase = preg_match('/[a-z]/', $password);
    $hasNumbers = preg_match('/\d/', $password);
    $hasSpecialChars = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);

    return strlen($password) >= $minLength && $hasUpperCase && $hasLowerCase && $hasNumbers && $hasSpecialChars;
}

function redirect($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);
    exit();
}

function formatTimeUnits($age, $unit)
{
    $ageDescription = '';

    switch ($unit) {
        case 'year':
            if ($age == 1) {
                $ageDescription = '1 rok';
            } elseif ($age % 10 == 2 || $age % 10 == 3 || $age % 10 == 4) {
                $ageDescription = "$age lata";
            } else {
                $ageDescription = "$age lat";
            }
            break;
        case 'month':
            $ageDescription = ($age == 1) ? '1 miesiąc' : "$age miesięcy";
            break;
        case 'day':
            $ageDescription = ($age == 1) ? '1 dzień' : "$age dni";
            break;
        default:
            $ageDescription = null;
            break;
    }

    return $ageDescription;
}
