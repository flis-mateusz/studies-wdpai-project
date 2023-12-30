<?php

require_once __DIR__ . '/../utils/logger.php';

class AttachmentManager
{
    private $attachment;

    const MAX_FILE_SIZE = 1024 * 1024 * 5;
    const SUPPORTED_TYPES = ['image/png', 'image/jpeg'];
    const UPLOAD_DIRECTORY = '/../public/images/uploads/';

    public function __construct($attachment)
    {
        $this->attachment = $attachment;
        Logger::debug($this->attachment);
    }

    public function save()
    {
        if (!$this->is_uploaded()) {
            throw new InvalidArgumentException('Plik nie został przesłany');
        }
        $this->validate();

        $new_file_name = uniqid() . '.' . pathinfo($this->attachment['name'], PATHINFO_EXTENSION);



        if (!move_uploaded_file($this->attachment['tmp_name'], dirname(__DIR__) . self::UPLOAD_DIRECTORY . $new_file_name)) {
            throw new Error('Błąd podczas przesyłania pliku');
        }
        return $new_file_name;
    }

    public function is_uploaded()
    {

        return is_uploaded_file($this->attachment['tmp_name']);
    }

    private function validate(): bool
    {
        if ($this->attachment['size'] > self::MAX_FILE_SIZE) {
            throw new InvalidArgumentException('Plik jest za duży');
        }

        if (!isset($this->attachment['type']) || !in_array($this->attachment['type'], self::SUPPORTED_TYPES)) {
            throw new InvalidArgumentException('Plik o tym rozrzeczeniu nie jest obsługiwany');
        }

        return true;
    }

    public static function delete($fileName): bool
    {
        $path = dirname(__DIR__). self::UPLOAD_DIRECTORY. $fileName;
        if (file_exists($path)) {
            return unlink($path);
        }
        return true;
    }
}
