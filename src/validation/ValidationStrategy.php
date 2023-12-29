<?php

require_once __DIR__ . '/../utils/utils.php';


class TwoOrMoreWordsValidation extends ValidationStrategy
{
    public function validate($value): bool
    {
        return isTwoOrMoreWords($value);
    }
}

class EmailValidation extends ValidationStrategy
{
    public function validate($value): bool
    {
        return isEmail($value);
    }
}

class NotEmptyValidation extends ValidationStrategy
{
    public function validate($value): bool
    {
        return isNotEmpty($value);
    }
}

class PhoneNumberValidation extends ValidationStrategy {
    public function validate($value): bool
    {
        return isPhoneNumber($value);
    }
}

class PasswordValidation extends ValidationStrategy
{
    private $skipEmpty;

    public function __construct($skipEmpty = false, $errorMessage = 'Hasło musi mieć co najmniej 8 znaków i zawierać duże i małe litery, cyfrę oraz znak specjalny')
    {
        $this->skipEmpty = $skipEmpty;
        parent::__construct($errorMessage);
    }
    public function validate($value): bool
    {
        if ($this->skipEmpty && isEmpty($value)) {
            return true;
        }
        return isPasswordStrong($value);
    }
}

class AreValuesSameValidation extends ValidationStrategy {
    private $otherFieldName;
    private $allowEmpty;

    public function __construct($errorMessage, $otherFieldName, $allowEmpty = false) {
        parent::__construct($errorMessage);
        $this->otherFieldName = $otherFieldName;
        $this->allowEmpty = $allowEmpty;
    }

    public function validate($value):bool {
        return true;
    }

    public function getOtherFieldName() {
        return $this->otherFieldName;
    }

    public function isAllowEmpty() {
        return $this->allowEmpty;
    }
}


abstract class ValidationStrategy
{
    private $errorMessage;

    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function sanitize($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    abstract public function validate($value): bool;
}
