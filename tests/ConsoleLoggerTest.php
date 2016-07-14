<?php
/*
 * Note that actual output can´t be tested. We can only test that it runs without errors.
 */

namespace Phrity\Log\Test;

use \Phrity\Log\ConsoleLogger;
use \Symfony\Component\Console\Output\OutputInterface;

class ConsoleLoggerTest extends \PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        // Include to enable overwriting of PHP functions.
        require_once('override/override.php');
    }

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

    public function testCliOptions()
    {
        $argv_original = $_SERVER['argv'];

        $_SERVER['argv'] = array_merge($argv_original, array('--no-op'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('--ansi'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('--no-ansi'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('--quiet'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('-q'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('-vvv'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('--debug'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('--verbose=3'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('--verbose 3'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('--verbose=2'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('--verbose 2'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('-v'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('--verbose=1'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('--verbose'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = array_merge($argv_original, array('--verbose 1'));
        $logger = new ConsoleLogger();

        $_SERVER['argv'] = $argv_original;
    }

    public function testNotCli()
    {
        // Override php_sapi_name function, return false
        global $override_php_sapi_name, $override_defined;
        $override_php_sapi_name = 'not_cli';
        $override_defined = false;

        $logger = new ConsoleLogger(OutputInterface::VERBOSITY_QUIET);
        $logger->debug("a {foo} message", ['foo' => 'bar']);

        $override_php_sapi_name = null;
        $override_defined = null;
    }

    public function testInvalidLevel()
    {
        $logger = new ConsoleLogger(OutputInterface::VERBOSITY_QUIET);
        $logger->log(1234, "a {foo} message", ['foo' => 'bar']);
    }

    public function testInvalidVerboisty()
    {
        $logger = new ConsoleLogger(OutputInterface::VERBOSITY_QUIET);
        $logger->log(1234, "a {foo} message", ['foo' => 'bar']);
    }
}
