<?php

require_once __DIR__ . '/ValidationStrategy.php';
require_once __DIR__ . '/../utils/utils.php';


class PostDataValidator
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
            if (!array_key_exists($field, $this->data)) {
                if ($strategy->canValueBeEmpty()) {
                    $this->sanitizedData[$field] = null;
                    continue;
                }
                $this->errors[$field] = $strategy->getErrorMessage();
                continue;
            }
            $value = $this->data[$field];

            if (isEmpty($value) && $strategy->canValueBeEmpty()) {
                $this->sanitizedData[$field] = null;
                continue;
            }

            $sanitizedValue = $strategy->sanitize($value);
            if ($sanitizedValue === false) {
                $this->errors[$field] = 'Wartość nie może zawierać znaków specjalnych';
                continue;
            }

            try {
                $convertedValue = $strategy->convertType($sanitizedValue);
            } catch (InvalidArgumentException $e) {
                $this->errors[$field] = $e->getMessage();
                continue;
            }

            $this->sanitizedData[$field] = $convertedValue;

            if ($strategy instanceof AreValuesSameValidation) {
                $otherFieldName = $strategy->getOtherFieldName();
                $otherValue = $this->sanitizedData[$otherFieldName] ?? '';

                if ($otherValue !== $sanitizedValue) {
                    $this->errors[$field] = $strategy->getErrorMessage();
                }
            } else {
                if (!$strategy->validate($convertedValue)) {
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

    public function getData()
    {
        return $this->data;
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
