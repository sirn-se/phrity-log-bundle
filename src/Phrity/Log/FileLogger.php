<?php

namespace Phrity\Log;

use \Phrity\Log\Util\InterpolationTrait;
use \Psr\Log\AbstractLogger;
use \Psr\Log\LoggerInterface;
use \Psr\Log\LogLevel;
use \Symfony\Component\Filesystem;

class FileLogger extends AbstractLogger implements LoggerInterface
{
    use InterpolationTrait;

    private $filepath;
    private $disabled = false;
    private $used_files = array();

    public function __construct($filepath)
    {
        if (empty($filepath) || !is_string($filepath)) {
            return $this->disable("Invalid file path specified");
        }
        $this->filepath = $filepath;
    }

    public function log($level, $message, array $context = array())
    {
        // If this logger is disabled, abort silently
        if ($this->disabled) {
            return;
        }

        // Resolve date-time holders
        $real_filepath = $this->resolvePathVariables();

        // Ensure valid directory structure
        $this->ensureFilepath($real_filepath);
        if ($this->disabled) {
            return;
        }

        // Write message to file
        $interpolated = $this->interpolate($message, $context) . "\n";
        if (file_put_contents($real_filepath, $interpolated, FILE_APPEND | LOCK_EX) === false) {
            return $this->disable("Failed writing to log file '{$real_filepath}'");
        }

        // Keep as being "safe to use"
        if (!in_array($real_filepath, $this->used_files)) {
            $this->used_files[] = $real_filepath;
        }
    }

    public function getUsedFiles()
    {
        return $this->used_files;
    }

    private function resolvePathVariables()
    {
        $real_filepath = $this->filepath;
        preg_match_all('/\{([a-zA-Z])\}/', $real_filepath, $matches);
        foreach ($matches[1] as $fchar) {
            $real_filepath = str_replace('{' . $fchar . '}', date($fchar), $real_filepath);
        }
        return $real_filepath;
    }

    private function ensureFilepath($real_filepath)
    {
        // Already wrote to this file, should be safe
        if (in_array($real_filepath, $this->used_files)) {
            return;
        }

        // Create directories
        $dir = dirname($real_filepath);
        if (!file_exists($dir)) {
            if (mkdir($dir, 0770, true) === false) {
                return $this->disable("Could not create directory '{$dir}'");
            }
        }

        // Verify file
        if (is_dir($real_filepath)) {
            return $this->disable("Target is a directory, can not write '{$real_filepath}'");
        }
    }

    /**
     * If the logger fails to write, it *should* trigger an error but without breaking execution.
     * On failure it *must* also stop any additional writing attempts.
     * If we don't disable further logging, we might end up in endless loop.
     */
    private function disable($error_msg)
    {
        $this->disabled = true;
        trigger_error($error_msg, E_USER_ERROR);
    }
}
