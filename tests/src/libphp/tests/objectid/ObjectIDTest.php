<?php
namespace libphp\tests\objectid;

use libphp\test\test_case;
use libphp\test\test_suite;
use libphp\objectid\id;
use libphp\objectid\increment_counter;
use libphp\objectid\objectid;
use libphp\objectid\objectid_builder;
use libphp\objectid\errors\increment_count_overflow;
use libphp\objectid\errors\backward_timestamp;

class ObjectIDTest extends test_case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function testCreateID() {
    $id = ObjectIDBuilder::create($this->counter)->build();
    $this->assert($id->getTimestamp() == $this->counter->getLastIncTime(), "get unpack timestamp");
    $this->assertEqual(substr(md5(gethostname()),0,3), $id->getMachineID());
    $this->assertEqual($id->getProcessID(), getmypid());

    $id = ObjectIDBuilder::create($this->counter)->processID(10)->build();
    $this->assertEqual(10, $id->getProcessID());

    $id = ObjectIDBuilder::create($this->counter)->machineName('test-machine')->build();
    $this->assertEqual(substr(md5('test-machine'),0,3), $id->getMachineID());
  }

  public function testEqual() {
    $id = ObjectIDBuilder::create($this->counter)->build();

    $compare_id = ObjectID::createWithString($id->toString());
    $this->assertTrue($id->equal($compare_id));

    $this->assertEqual($id->getTimestamp(), $compare_id->getTimestamp());
    $this->assertEqual($id->getMachineID(), $compare_id->getMachineID());
    $this->assertEqual($id->getProcessID(), $compare_id->getProcessID());
  }

  public function testNotDuplicatedIDs() {
    $ids = $this->generateIDs(1000);
    $this->assert(count($ids) == 1000, 'not duplicated ids');
  }

  public function testResetIncrement() {
    $id = ObjectIDBuilder::create($this->counter)->build();
    $this->assert(1 == $this->counter->get_increment(), 'inc is 1');
    $id = ObjectIDBuilder::create($this->counter)->build();
    $this->assert(2 == $this->counter->get_increment(), 'inc is 2');

    $this->counter->inc_current_time();
    $id = ObjectIDBuilder::create($this->counter)->build();
    $this->assert(1 == $this->counter->get_increment(), 'inc is 1');
  }

  public function testThrowBackwardTimestamp() {
    $this->assert(true == $this->processGenerateIDs(1));
    $this->counter->decCurrentTime();
    $this->assert(false == $this->processGenerateIDs(1));
  }

  public function testThrowIncrementCountOverflow() {
    $this->assert(true == $this->processGenerateIDs($this->getMaxIncrementCount()));
    $this->assert(false == $this->processGenerateIDs($this->getMaxIncrementCount() + 1));
  }

  private function processGenerateIDs($count) {
    try {
      $this->generateIDs($count);
    }
    catch (BackwardTimestamp $e) {
      return false;
    }
    catch (IncrementCountOverflow $e) {
      return false;
    }
    return true;
  }

  private function generateIDs($count) {
    $ids = array();
    for ($i = 0; $i < $count; $i++) {
      $id = ObjectIDBuilder::create($this->counter)->build();
      array_push($ids, $id->toString());
    }
    return array_unique($ids);
  }

  public function testHexString() {
    $id = ObjectIDBuilder::create($this->counter)->build();
    $hex_str = $id->toString();
    $compare_id = ObjectID::createWithString($hex_str);
    $this->assertEqual($id->toString(), $compare_id->toString());
  }


  public function setUp() {
    $this->counter = $this->createCounter();
    $timestamp = time();
    $this->counter->set_current_time($timestamp);

  }

  public function tearDown() {
  }

  private function createCounter() {
    return new test_increment_counter();
  }

  private function getMaxIncrementCount() {
    return ObjectID::MAX_INCREMENT_COUNT_PER_SEC;
  }

  private $counter;

  public static function createSuite() {
    $suite = new test_suite('ObjectIDTest');
    $suite->add(new ObjectIDTest('testCreateID'));
    $suite->add(new ObjectIDTest('testEqual'));
    $suite->add(new ObjectIDTest('testNotDuplicatedIDs'));
    $suite->add(new ObjectIDTest('testResetIncrement'));
    $suite->add(new ObjectIDTest('testThrowBackwardTimestamp'));
    $suite->add(new ObjectIDTest('testThrowIncrementCountOverflow'));
    $suite->add(new ObjectIDTest('testHexString'));
    return $suite;
  }
}
