<?php
/*
 * Note that actual output can´t be tested. We can only test that ir runs without errors.
 */

namespace Phrity\Log\Test;

use \Phrity\Log\Logger\Console;
use \Symfony\Component\Console\Output\OutputInterface;

class ConsoleLoggerTest extends \PHPUnit_Framework_TestCase
{

    public function testLogLevels()
    {
        $logger = new Console(OutputInterface::VERBOSITY_QUIET);
        $logger->critical("a message");
        $logger->emergency("a message");
        $logger->alert("a message");
        $logger->error("a message");
        $logger->warning("a message");
        $logger->notice("a message");
        $logger->info("a message");
        $logger->debug("a message");
    }

    public function testContext()
    {
        $logger = new Console(OutputInterface::VERBOSITY_QUIET);
        $logger->debug("a message", ['foo' => 'bar']);
    }
}
