<?php

require_once 'JsonResponse.php';

class PostFormResponse extends JsonResponse
{
    private $errorFields;

    public function __construct($errorFields = [])
    {
        $this->errorFields = $errorFields;
        if (empty($errorFields)) {
            $this->setStatusCode(200);
        } else {
            $this->setStatusCode(400);
        }
    }

    public function addErrorField($fieldName, $errorMessage, $statusCode=400)
    {
        $this->setStatusCode($statusCode);
        $this->errorFields[$fieldName] = $errorMessage;
    }

    public function setErrorFields(array $errorFields) {
        $this->setStatusCode(400);
        $this->errorFields = $errorFields;
    }

    public function send()
    {
        if ($this->errorFields) {
            $this->data['invalidFields'] = $this->errorFields;
        }
        parent::send();
    }
}
