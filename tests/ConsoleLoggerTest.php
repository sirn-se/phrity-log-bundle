<?php
/*
 * Note that actual output can´t be tested. We can only test that ir runs without errors.
 */

namespace Phrity\Log\Test;

use \Phrity\Log\ConsoleLogger;
use \Symfony\Component\Console\Output\OutputInterface;

class ConsoleLoggerTest extends \PHPUnit_Framework_TestCase
{

    public function testLogLevels()
    {
        $logger = new ConsoleLogger(OutputInterface::VERBOSITY_QUIET);
        $logger->critical("a critical message");
        $logger->emergency("an emergency message");
        $logger->alert("an alert message");
        $logger->error("an error message");
        $logger->warning("a warning message");
        $logger->notice("a notice message");
        $logger->info("an info message");
        $logger->debug("a debug message");
    }

    public function testContext()
    {
        $logger = new ConsoleLogger(OutputInterface::VERBOSITY_QUIET);
        $logger->debug("a {foo} message", ['foo' => 'bar']);
    }
}
