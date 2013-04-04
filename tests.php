<?php
require_once("./runner.php");
header("Content-type:text/plain");

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
$str = "";
for($i = 0; $i < 1000000; $i++) {
  $str .= "a";
}
$runner->registerTestCase("5 1MB get/set", function($cache) use ($str) {
  for($i = 1; $i<=5; $i++) {
    $cache->set($i, $str);
  }
  for($i = 1; $i<=5; $i++) {
    $r = $cache->get($i);
    if($r != $str) throw new Exception("expected: " . $i . " was" . $r);
  }
});
/*
try {
  $runner->warmup();
} catch (Exception $e) {
  echo "warmup error:" . $e;
}
*/
try {
  $runner->run();
} catch (Exception $e) {
  echo "testrun error:" . $e;
}
