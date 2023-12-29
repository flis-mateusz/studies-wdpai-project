<?php

require_once __DIR__ . '/ValidationStrategy.php';

class PostFormValidator
{
    private $data;
    private $errors = [];
    private $validationStrategies = [];
    private $sanitizedData = [];

    public function __construct($formData)
    {
        $this->data = $formData;
    }

    public function addField($fieldName, ValidationStrategy $strategy)
    {
        $this->validationStrategies[$fieldName] = $strategy;
    }

    /**
     * Validate the form data
     *
     * @return boolean true if the form data is valid, false otherwise
     */
    public function validate()
    {
        foreach ($this->validationStrategies as $field => $strategy) {
            $value = $this->data[$field] ?? '';
            $sanitizedValue = $strategy->sanitize($value);
            $this->sanitizedData[$field] = $sanitizedValue;

            if ($strategy instanceof AreValuesSameValidation) {
                $otherFieldName = $strategy->getOtherFieldName();
                $otherValue = $this->sanitizedData[$otherFieldName] ?? '';
                if (!$strategy->validate($sanitizedValue, $otherValue)) {
                    $this->errors[$field] = $strategy->getErrorMessage();
                }
            } else {
                if (!$strategy->validate($sanitizedValue)) {
                    $this->errors[$field] = $strategy->getErrorMessage();
                }
            }
        }

        return empty($this->errors);
    }

    public function getSanitizedData()
    {
        return $this->sanitizedData;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFieldValue($fieldName)
    {
        return $this->data[$fieldName] ?? null;
    }
}
