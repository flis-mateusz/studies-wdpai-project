<?php

require_once 'JsonResponse.php';

class ValidationErrorResponse extends JsonResponse
{
    private $errorFields;

    public function __construct($data = [], $errorFields = [])
    {
        parent::__construct($data);
        $this->errorFields = $errorFields;
        $this->setStatusCode(409);
    }

    public function addErrorField($fieldName, $errorMessage)
    {
        $this->errorFields[$fieldName] = $errorMessage;
    }

    public function setData($data)
    {
        $this->setError($data);
    }

    public function send()
    {
        if ($this->errorFields) {
            $this->data['invalidFields'] = $this->errorFields;
        }
        parent::send();
    }
}
