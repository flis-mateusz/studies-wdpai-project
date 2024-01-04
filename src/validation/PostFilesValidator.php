<?php

require_once __DIR__ . '/../managers/AttachmentManager.php';


class PostFilesValidator
{
    private $data;
    private $fields = [];
    private $errors = [];
    private $files = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function addField($fieldName, $errorMessage, $required = true)
    {
        $this->fields[] = [
            'fieldName' => $fieldName,
            'isRequired' => $required,
            'errorMessage' => $errorMessage
        ];
    }

    private function addError($fieldName, $errorMessage)
    {
        $this->errors[$fieldName] = $errorMessage;
    }

    public function validate()
    {
        foreach ($this->fields as $field) {
            $fieldName = $field['fieldName'];
            $isRequired = $field['isRequired'];
            $errorMessage = $field['errorMessage'];

            if (!array_key_exists($fieldName, $this->data)) {
                if ($isRequired) {
                    $this->addError($fieldName, $errorMessage);
                }
                continue;
            }

            $file = new AttachmentManager($this->data[$fieldName]);
            if ($file->isUploaded()) {
                $this->files[$fieldName] = $file;
            } else if ($isRequired) {
                $this->addError($fieldName, $errorMessage);
            }
        }
        return empty($this->errors);
    }


    public function getErrors()
    {
        return $this->errors;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getFile($fieldName): null | AttachmentManager
    {
        return $this->files[$fieldName] ?? null;
    }
}
