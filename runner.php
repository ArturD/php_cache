<?php
require_once("./cache.php");

class TestCase {
  public $name;
  public $callable;

  public function run($cache, $seed) {
    $f = $this->callable;
    $start = microtime(true);
    $f($cache, $seed);
    $end = microtime(true);
    return $end - $start;
  }
}

class CacheMethod {
  public $name;
  public $cacheService;
}

class TestRunner {
  private $cacheMethods = array();
  private $testCases = array();

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

  public function run($seed) {
    foreach($this->testCases as $j => $case) {
      echo "CASE: " . $case->name . "\n";
      foreach($this->cacheMethods as $i => $method) {
        echo "  METHOD: " . $method->name;
        $time = $case->run($method->cacheService, $seed);
        echo "  TIME: " . number_format($time, 3) . "\n";
      }
    }
  }
}
