<?php
class JsonResponse
{
    private $success;
    private $message;
    private $data;

    public function __construct($success = false, $message = '', $data = [])
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
    }

    public function setSuccess($success)
    {
        $this->success = $success;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function send()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data
        ]);
        exit;
    }
}
