<?php

namespace App\Helper;

use Exception;

class LogHelper
{
    private static bool $log_enable = true;

    public function info($message)
    {
        $this->log("INFO", $message);
    }

    public function error($message)
    {
        $this->log("ERROR", $message);
    }

    public function warning($message)
    {
        $this->log('WARNING', $message);
    }

    public function exception_error($message, Exception $e)
    {
        $this->log("EXCEPTION ERROR",
            "message: " . $message .
            " \r\nline: " . $e->getLine() .
            " code: " . $e->getCode() .
            " \r\nmessage: " . $e->getMessage() .
            " \r\nfile: " . $e->getFile()
        );
    }

    private function log($prefix, $message)
    {
        if (self::$log_enable) {
            print_r(date("d.m.y H:i:s") . " [$prefix] $message\n");
        }
    }

    /**
     * @param bool $log_enable
     */
    public static function setLogEnable(bool $log_enable): void
    {
        self::$log_enable = $log_enable;
    }
}
