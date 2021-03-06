<?php
require_once("./runner.php");
header("Content-type:text/plain");

// create 1 MB string
$str = "";
for($i = 0; $i < 1000000; $i++) {
  $str .= "a";
}

$runner = new TestRunner();

// register cache services
if( function_exists("apc_store") ) {
  $runner->registerCache("Apc", function() {
      return new ApcCache();
    });
}
$runner->registerCache("Memcache", function() {
    $mc = new Memcache();
    $mc->connect("localhost");
    return new MemcacheCache($mc);
  });
$runner->registerCache("Array", function() {
    return new ArrayCache();
  });
if( function_exists("xcache_set") ) {
  $runner->registerCache("XCache", function() {
      return new XCacheCache();
    });
}
// register test cases
$runner->registerTestCase("1000 small get/set", function($cache, $seed) {
  for($i = 1; $i<=1000; $i++) {
    $cache->set($i . $seed, $i);
  }
  for($i = 1; $i<=1000; $i++) {
    $r = $cache->get($i . $seed);
    if($r != $i) throw new Exception("expected: " . $i . " was" . $r);
  }
});
$runner->registerTestCase("5 1MB get/set", function($cache, $seed) use ($str) {
  for($i = 1; $i<=5; $i++) {
    $cache->set($i . $seed, $str);
  }
  for($i = 1; $i<=5; $i++) {
    $r = $cache->get($i . $seed);
    if($r != $str) throw new Exception("");
  }
});

$runner->registerTestCase("1 set/1000 gets", function($cache, $seed) {
  $cache->set($seed, 1);
  for($i = 1; $i<=1000; $i++) {
    $r = $cache->get($seed);
    if($r != 1) throw new Exception("expected: 1 was" . $r);
  }
});


try {
  $seed = "_seed_" . rand();
  $runner->run($seed);
} catch (Exception $e) {
  echo "testrun error:" . $e;
}
