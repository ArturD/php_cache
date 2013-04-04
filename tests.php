<?php
require_once("./runner.php");

$runner = new TestRunner();

// register cache services
$runner->registerCache("Apc", function() {
    return new ApcCache();
  });
$runner->registerCache("Memcache", function() {
    $mc = new Memcache();
    $mc->connect("localhost");
    return new MemcacheCache($mc);
  });

// register test cases
$runner->registerTestCase("1000 small get/set", function($cache) {
  for($i = 1; $i<=1000; $i++) {
    $cache->set($i, $i);
  }
  for($i = 1; $i<=1000; $i++) {
    $r = $cache->get($i);
    if($r != $i) throw new Exception("expected: " . $i . " was" . $r);
  }
});
/*
try {
  $runner->warmup();
} catch (Exception $e) {
  echo "warmup error:" . $e;
}
*/
$runner->run();
