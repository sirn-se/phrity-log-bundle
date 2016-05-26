<?php

namespace Phrity\Log;

use \Phrity\Log\Util\InterpolationTrait;
use \Psr\Log\AbstractLogger;
use \Psr\Log\LoggerInterface;
use \Psr\Log\LogLevel;
use \Symfony\Component\Console\Input\ArgvInput;
use \Symfony\Component\Console\Output\ConsoleOutput;
use \Symfony\Component\Console\Output\OutputInterface;

class ConsoleLogger extends AbstractLogger implements LoggerInterface
{
    use InterpolationTrait;

    private $format;
    private $console;

    public function __construct($verbosity = null)
    {
        if (!$this->isCli()) {
            return;
        }
        $this->console = new ConsoleOutput();
        $this->setStyle($verbosity);
    }

    public function log($level, $message, array $context = array())
    {
        if (!$this->isCli()) {
            return;
        }
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

    private function isCli()
    {
        return php_sapi_name() == 'cli' || defined('STDIN');
    }

    private function setStyle($verbosity = null)
    {
        $this->console->setVerbosity($default_verbosity);
        $input = new ArgvInput();

        // Set decoration
        if ($input->hasParameterOption(array('--ansi'))) {
            $this->console->setDecorated(true);
        } elseif ($input->hasParameterOption(array('--no-ansi'))) {
            $this->console->setDecorated(false);
        }

        // Set verbosity
        if (is_numeric($verbosity)) {
            $this->console->setVerbosity($verbosity);
        } elseif ($input->hasParameterOption(array('--quiet', '-q'))) {
            $this->console->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        } elseif ($input->hasParameterOption(array('-vvv', '--debug'))
                || $input->hasParameterOption('--verbose=3')
                || $input->getParameterOption('--verbose') === 3) {
            $this->console->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        } elseif ($input->hasParameterOption('-vv')
                || $input->hasParameterOption('--verbose=2')
                || $input->getParameterOption('--verbose') === 2) {
            $this->console->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
        } elseif ($input->hasParameterOption('-v')
                || $input->hasParameterOption('--verbose=1')
                || $input->hasParameterOption('--verbose')
                || $input->getParameterOption('--verbose') === 1) {
            $this->console->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        } else {
            $this->console->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
        }
    }
}
