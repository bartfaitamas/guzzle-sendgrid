<?php

error_reporting(E_ALL | E_STRICT);

// Ensure that composer has installed all dependencies
if (!file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'composer.lock')) {
    die("Dependencies must be installed using composer:\n\ncomposer.phar install --install-suggests\n\n"
        . "See https://github.com/composer/composer/blob/master/README.md for help with installing composer\n");
}

require_once 'PHPUnit/TextUI/TestRunner.php';

// Register an autoloader for the client being tested
spl_autoload_register(function($class) {
    if (0 === strpos($class, 'Guzzle\SendGrid')) {
        $class = str_replace('Guzzle\SendGrid', '', $class);
        if ('\\' != DIRECTORY_SEPARATOR) {
            $class = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Guzzle/SendGrid' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        } else {
            $class = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Guzzle\SendGrid' . DIRECTORY_SEPARATOR . $class . '.php';
        }
        if (file_exists($class)) {
            require $class;
        }
    }
});

// Include the composer autoloader
$loader = require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . '.composer' . DIRECTORY_SEPARATOR . 'autoload.php');

// Register services with the GuzzleTestCase
Guzzle\Tests\GuzzleTestCase::setMockBasePath(__DIR__ . DIRECTORY_SEPARATOR . 'mock');

$builderParams = array('test.sendgrid' => array('class' => 'Guzzle\SendGrid\SendGridClient',
                                                'params' => array('username' => $_SERVER['API_USER'],
                                                                  'password' => $_SERVER['API_PASSWORD'])));

Guzzle\Tests\GuzzleTestCase::setServiceBuilder(Guzzle\Service\ServiceBuilder::factory($builderParams));