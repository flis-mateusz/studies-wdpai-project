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

function isEmpty($str)
{
    return !isset($str) || strlen($str) === 0;
}

function isNotEmpty($str)
{
    return isset($str) && strlen($str) > 0;
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
