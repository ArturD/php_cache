<?php
require_once("./cache.php");

class TestCase {
  public $name;
  public $callable;

  public function run($cache) {
    $f = $this->callable;
    $start = microtime(true);
    $f($cache);
    $end = microtime(true);
    return $end - $start;
  }
}

class CacheMethod {
  public $name;
  public $cacheService;
}

class TestRunner {
  private $cacheMethods = [];
  private $testCases = [];

  public function registerCache($name, $factoryMethod) {
    $method = new CacheMethod();
    $method->name = $name;
    $method->cacheService = $factoryMethod();
    $this->cacheMethods[] = $method;
  }

  public function registerTestCase($name, $callable) {
    $case = new TestCase();
    $case->name = $name;
    $case->callable = $callable;
    $this->testCases[] = $case;
  }

  public function warmup() {
    foreach($this->cacheMethods as $i => $method) {
      foreach($this->testCases as $j => $case) {
        $case->run($method->cacheService);
      }
    }
  }

  public function run() {
    foreach($this->cacheMethods as $i => $method) {
      echo "METHOD: " . $method->name;
      foreach($this->testCases as $j => $case) {
        echo "  CASE: " . $case->name;
        $time = $case->run($method->cacheService);
        echo "  TIME: " . $time;
      }
    }
  }
}
