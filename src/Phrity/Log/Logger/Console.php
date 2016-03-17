<?php

namespace Phrity\Log\Logger;

use \Phrity\Log\Util\InterpolationTrait;
use \Psr\Log\AbstractLogger;
use \Psr\Log\LoggerInterface;
use \Psr\Log\LogLevel;
use \Symfony\Component\Console\Output\ConsoleOutput;
use \Symfony\Component\Console\Output\OutputInterface;

class Console extends AbstractLogger implements LoggerInterface
{
    use InterpolationTrait;

    private $format;
    private $console;

    public function __construct($verbosity = OutputInterface::VERBOSITY_NORMAL)
    {
        $this->console = new ConsoleOutput();
        $this->console->setVerbosity($verbosity);
    }

    public function log($level, $message, array $context = array())
    {
        $message = $this->interpolate($message, $context);
        $message = $this->formatMessageByLevel($level, $message);
        $verbosity = $this->getVerbosityByLevel($level);
        $this->console->writeln($message, $verbosity);
    }

    private function getVerbosityByLevel($level)
    {
        switch ($level) {
            case LogLevel::CRITICAL:
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
            case LogLevel::ERROR:
            case LogLevel::WARNING:
                return OutputInterface::VERBOSITY_NORMAL;
            case LogLevel::NOTICE:
                return OutputInterface::VERBOSITY_VERBOSE;
            case LogLevel::INFO:
                return OutputInterface::VERBOSITY_VERY_VERBOSE;
            case LogLevel::DEBUG:
                return OutputInterface::VERBOSITY_DEBUG;
        }
    }

    private function formatMessageByLevel($level, $message)
    {
        switch ($level) {
            case LogLevel::CRITICAL:
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
                return "<bg=red;fg=yellow>{$message}</>";
            case LogLevel::ERROR:
                return "<bg=red;fg=white>{$message}</>";
            case LogLevel::WARNING:
                return "<bg=yellow;fg=black>{$message}</>";
            case LogLevel::NOTICE:
                return "<bg=cyan;fg=black>{$message}</>";
            case LogLevel::INFO:
                return "<fg=cyan>{$message}</>";
            case LogLevel::DEBUG:
                return"<fg=yellow>{$message}</>";
        }
    }
}
