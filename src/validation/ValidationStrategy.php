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

class PhoneNumberValidation extends ValidationStrategy
{
    public function validate($value): bool
    {
        return isPhoneNumber($value);
    }
}

class MinMaxLengthValidation extends RangeValidation
{
    public function validate($value): bool
    {
        return inRange(strlen($value), $this->min, $this->max);
    }
}

class PasswordValidation extends ValidationStrategy
{
    public function __construct($errorMessage = 'Hasło musi mieć co najmniej 8 znaków i zawierać duże i małe litery, cyfrę oraz znak specjalny')
    {
        parent::__construct($errorMessage);
    }
    public function validate($value): bool
    {
        return isPasswordStrong($value);
    }
}

class AreValuesSameValidation extends ValidationStrategy
{
    private $otherFieldName;
    public function __construct($errorMessage, $otherFieldName)
    {
        parent::__construct($errorMessage);
        $this->otherFieldName = $otherFieldName;
    }

    public function validate($value): bool
    {
        throw new Error('AreValuesSameValidation validation takes place in PostDataValidator');
    }

    public function getOtherFieldName()
    {
        return $this->otherFieldName;
    }
}

class InArrayValidation extends ValidationStrategy
{
    private $array;

    public function __construct($errorMessage, $array)
    {
        parent::__construct($errorMessage);
        $this->array = $array;
    }

    public function validate(mixed $value): bool
    {
        if (is_array($value)) {
            return empty(array_diff($value, $this->array));
        }

        return in_array($value, $this->array);
    }
}

class InCheckboxArrayValidation extends ValidationStrategy
{
    private $array;

    public function __construct($errorMessage, $array)
    {
        parent::__construct($errorMessage);
        $this->array = $array;
    }

    public function validate(mixed $value): bool
    {
        $arr = [];
        foreach ($value ?? [] as $id => $value) {
            $animalFeatures[] = $id;
        }
        return empty(array_diff($arr, $this->array));
    }
}

class SanitizeOnly extends ValidationStrategy
{
    public function __construct()
    {
        parent::__construct('Sanitization error');
    }

    public function validate($value): bool
    {
        return true;
    }
}




class RangeValidation extends ValidationStrategy
{
    protected $min;
    protected $max;

    public function __construct($type, $errorMessage, $min = null, $max = null)
    {
        parent::__construct($errorMessage);
        parent::setConvertToType($type);
        $this->min = $min;
        $this->max = $max;
    }

    public function validate($value): bool
    {
        return inRange($value, $this->min, $this->max);
    }
}

abstract class ValidationStrategy
{
    private $errorMessage;
    protected $convertToType = null;
    private $rejectHtmlspecialchars;
    private $canValueBeEmpty;
    private $santization;

    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
        $this->rejectHtmlspecialchars = false;
        $this->canValueBeEmpty = false;
        $this->santization = true;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function sanitize(mixed $value)
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitize'], $value);
        }

        $encodedString = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        if ($this->rejectHtmlspecialchars && $encodedString !== $value) {
            return false;
        }
        return $encodedString;
    }

    public function convertType($value)
    {
        if ($this->convertToType === null) {
            return $value;
        }

        switch ($this->convertToType) {
            case 'int':
                if (!is_numeric($value)) {
                    throw new InvalidArgumentException($this->errorMessage);
                }
                return intval($value);
            case 'float':
                if (!is_numeric($value)) {
                    throw new InvalidArgumentException($this->errorMessage);
                }
                return floatval($value);
            default:
                return $value;
        }
    }

    public function setConvertToType($type)
    {
        $this->convertToType = $type;
        return $this;
    }

    public function setRejectHTMLSpecialChars(bool $rejectHtmlspecialchars)
    {
        $this->rejectHtmlspecialchars = $rejectHtmlspecialchars;
        return $this;
    }

    public function setCanValueBeEmpty(bool $canValueBeEmpty)
    {
        $this->canValueBeEmpty = $canValueBeEmpty;
        return $this;
    }

    public function canValueBeEmpty(): bool
    {
        return $this->canValueBeEmpty;
    }

    public function setSantization(bool $santization)
    {
        $this->santization = $santization;
        return $this;
    }

    abstract public function validate($value): bool;
}
