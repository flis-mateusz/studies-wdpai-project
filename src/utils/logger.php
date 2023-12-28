<?php

class Logger {
    static public function debug($message) {
        file_put_contents('php://stderr', print_r($message, TRUE), FILE_APPEND);
    }
}