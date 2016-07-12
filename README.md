[![Build Status](https://travis-ci.org/sirn-se/phrity-log-bundle.png)](https://travis-ci.org/sirn-se/phrity-log-bundle)

# Log bundle

This library utilizes the [PSR-3 compatible](https://github.com/php-fig/log) interface.


## The Console Logger

This is a basic logger that prints all log messages to the console.
Note that it will only output if PHP is running in CLI mode, otherwise output will be supressed.

For output, this logger uses the
[Symfony Console](http://symfony.com/doc/current/components/console/introduction.html) component.

```php
$logger = new \Phrity\Log\ConsoleLogger();

$logger->info("My message");
// => "My message"

$logger->info("Got {something} in {somewhere}!", array(
  'something' => 'a spoon',
  'somewhere' => 'my eye',
));
// => "Got a spoon in my eye!"
```

### Verbosity

When running in console, verbosity may be set by using various flags.

  * By default, levels _emergency_, _alert_, _critical_, _error_, and _warning_ will be output
  * `-v` for verbose — adds level _notice_
  * `-vv` for very verbose — adds level _info_
  * `-vvv` or `--debug` for debug — adds level _debug_
  * `-q` or `--quiet` — No output

  * By default, or using `--ansi`, output will use colors
  * Using `--no-ansi` will supress colors.

## The File Logger

Log messages to file.

```php
$logger = new \Phrity\Log\FileLogger('path/to/logfile.log');

$logger->info("My message");
// => "My message\n"

$logger->info("{timestamp} | You got logged!", array(
  'timestamp' => date('Y-m-d'),
));
// => "2016-07-11 | You got logged!\n"
```

### Dynamic file paths

The file reference supports all format keys used by
[date()](http://php.net/manual/en/function.date.php) by wrapping them in ```{ }```.

```php
$logger = new \Phrity\Log\FileLogger("path/to/{Y}/{m}/file_{d}.log");
$logger->info("My message");
```
