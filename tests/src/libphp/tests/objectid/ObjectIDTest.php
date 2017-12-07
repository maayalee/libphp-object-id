<?php
namespace libphp\tests\objectid;

use libphp\test\TestCase;
use libphp\test\TestSuite;
use libphp\objectid\ID;
use libphp\objectid\IncrementCounter;
use libphp\objectid\ObjectID;
use libphp\objectid\ObjectIDBuilder;
use libphp\objectid\errors\IncrementCountOverflow;
use libphp\objectid\errors\BackwardTimestamp;

class ObjectIDTest extends TestCase {
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
    $this->assert(1 == $this->counter->getIncrement(), 'inc is 1');
    $id = ObjectIDBuilder::create($this->counter)->build();
    $this->assert(2 == $this->counter->getIncrement(), 'inc is 2');

    $this->counter->incCurrentTime();
    $id = ObjectIDBuilder::create($this->counter)->build();
    $this->assert(1 == $this->counter->getIncrement(), 'inc is 1');
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
    $this->counter->setCurrentTime($timestamp);

  }

  public function tearDown() {
  }

  private function createCounter() {
    return new TestIncrementCounter();
  }

  private function getMaxIncrementCount() {
    return ObjectID::MAX_INCREMENT_COUNT_PER_SEC;
  }

  private $counter;

  public static function createSuite() {
    $suite = new TestSuite('ObjectIDTest');
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
