<?php
require_once('vendor/autoload.php');

ini_set('display_errors', 'on');

date_default_timezone_set('asia/seoul');

use libphp\test\test_runner;
use libphp\tests\test\test_case_test;
use libphp\tests\test\test_runner_test;
use libphp\tests\object_id\object_id_test;

$runner = new test_runner();
$runner->add(test_case_test::create_suite());
$runner->add(test_runner_test::create_suite());
$runner->add(object_id_test::create_suite());
$runner->run();

echo $runner->summary(). PHP_EOL;
