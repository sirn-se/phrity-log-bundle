<?php

namespace Phrity\Log\Test;

use \Phrity\Log\FileLogger;

class FileLoggerTest extends \PHPUnit_Framework_TestCase
{

    private $dir;

    public static function setUpBeforeClass()
    {
        // Include to enable overwriting of PHP file functions.
        require_once('override/override.php');
    }

    public function setUp()
    {
        $this->dir = sys_get_temp_dir() . "/file-logger-test/" . uniqid();
    }

    public function tearDown()
    {
        exec("rm -rf {$this->dir}");
    }

    public function testBasicLog()
    {
        $logger = new FileLogger("{$this->dir}/basic.log");
        $logger->critical("a critical message");
        $logger->emergency("an emergency message");
        $logger->alert("an alert message");
        $logger->error("an error message");
        $logger->warning("a warning message");
        $logger->notice("a notice message");
        $logger->info("an info message");
        $logger->debug("a debug message");

        $files = $logger->getUsedFiles();
        $this->assertCount(1, $files);
        $this->assertFileExists($files[0]);
        $this->assertFileEquals(__DIR__ . '/fixtures/file-logger-basic.txt', $files[0]);
    }

    public function testContextLog()
    {
        $logger = new FileLogger("{$this->dir}/context.log");
        $logger->debug("a {foo} message", ['foo' => 'bar']);

        $files = $logger->getUsedFiles();
        $this->assertCount(1, $files);
        $this->assertFileExists($files[0]);
        $this->assertFileEquals(__DIR__ . '/fixtures/file-logger-context.txt', $files[0]);
    }

    public function testDateTimeLog()
    {
        $logger = new FileLogger("{$this->dir}/{Y}-{m}-{d}-{H}-{i}-{s}.log");
        $logger->alert("an alert message");

        $files = $logger->getUsedFiles();
        $this->assertCount(1, $files);
        $this->assertFileExists($files[0]);
        $this->assertRegExp(
            "|^{$this->dir}/[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}.log$|",
            $files[0]
        );
        $this->assertFileEquals(__DIR__ . '/fixtures/file-logger-datetime.txt', $files[0]);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Invalid file path specified
     */
    public function testInvalidFile()
    {
        $logger = new FileLogger(array());
        $this->assertEmpty($logger->getUsedFiles());
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Could not create directory
     */
    public function testDirectoryCreationFailure()
    {
        // Override mkdir function, return false
        global $override_mkdir;
        $override_mkdir = false;

        $logger = new FileLogger("{$this->dir}/failure.log");
        $logger->alert("an alert message");
        $this->assertEmpty($logger->getUsedFiles());

        $override_mkdir = null;
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Target is a directory, can not write
     */
    public function testTargetIsDirectoryFailure()
    {
        // Override is_dir function, return true
        global $override_is_dir;
        $override_is_dir = true;

        $logger = new FileLogger("{$this->dir}/failure.log");
        $logger->alert("an alert message");
        $this->assertEmpty($logger->getUsedFiles());
        $override_is_dir = null;
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Failed writing to log file
     */
    public function testFileWriteFailure()
    {
        // Override file_put_contents function, return false
        global $override_file_put_contents;
        $override_file_put_contents = false;

        $logger = new FileLogger("{$this->dir}/failure.log");
        $logger->alert("an alert message");
        $this->assertEmpty($logger->getUsedFiles());

        $override_file_put_contents = null;
    }
}
