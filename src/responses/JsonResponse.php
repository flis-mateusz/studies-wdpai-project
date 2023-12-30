<?php
class JsonResponse
{
    private $status_code;
    protected $error;
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

    public function appendData($data)
    {
        $this->data = array_merge($this->data, $data);
    }

    public function setError($data, $status_code = 400)
    {
        $this->status_code = $status_code;
        $this->error = $data;
    }

    public function setStatusCode($status_code)
    {
        $this->status_code = $status_code;
    }

    public function send()
    {
        header('Content-Type: application/json');
        http_response_code($this->status_code);

        $response = [
            'status' => $this->status_code,
        ];

        if (!empty($this->data)) {
            $response['data'] = $this->data;
        }

        if (!empty($this->error)) {
            $response['error'] = $this->error;
        }

        echo json_encode($response);
        exit;
    }
}
