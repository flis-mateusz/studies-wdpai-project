<?php

class PostDataValidator
{
    private $sanitizedData = [];

    public function sanitizeData($postData, $fields, $checkAllEmpty = false)
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $postData)) {
                throw new Exception("Brak wymaganego pola: $field");
            }

            $value = $postData[$field];

            // Sprawdzanie, czy pole jest puste, jeśli checkAllEmpty jest ustawione na true
            if ($checkAllEmpty && trim($value) === '') {
                throw new Exception("Pole $field nie może być puste");
            }

            $this->sanitizedData[$field] = $this->sanitize($value);
        }
    }

    private function sanitize($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function getSanitizedData()
    {
        return $this->sanitizedData;
    }
}
