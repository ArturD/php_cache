<?php

interface ICache {
  function get($key);
  function set($key, $val);
}

class ApcCache implements ICache {
  function get($key) {
    return apc_fetch((string)$key);
  }
  function set($key, $val) {
    return apc_store((string)$key, $val);
  }
}

class MemcacheCache implements ICache {
  private $connection;
  function __construct($connection) {
    $this->connection = $connection;
  }

  function get($key) {
    return $this->connection->get((string)$key);
  }
  function set($key, $val) {
    return $this->connection->set((string)$key, $val);
  }
}

class ArrayCache implements ICache {
  private $array = array();
  function get($key) {
    return $this->array[(string)$key];
  }
  function set($key, $val) {
    $this->array[(string)$key] = $val;
    return true;
  }
}

class XCacheCache implements ICache {
  function get($key) {
    return xcache_get((string)$key);
  }
  function set($key, $val) {
    return xcache_set((string)$key, $val);
  }
}

