<?php
class JsonResponse
{
    private $status_code;
    protected $data;

    public function __construct($data = [])
    {
        $this->status_code = 200;
        $this->data = $data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setError($data)
    {
        $this->data['error'] = $data;
    }

    public function setStatusCode($status_code)
    {
        $this->status_code = $status_code;
    }

    public function send()
    {
        header('Content-Type: application/json');
        http_response_code($this->status_code);
        echo json_encode([
            'status' => $this->status_code,
            'data' => $this->data
        ]);
        exit;
    }
}
