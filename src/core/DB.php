<?php

namespace Clara\core;

class DB extends \PDO
{

  public function __construct(
    string $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR,
    string $user = DB_USER,
    string $password = DB_PASS,
    array $options = []
  ) {
    $defaultOptions  = array(
      \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
      \PDO::ATTR_EMULATE_PREPARES   => FALSE,
    );
    $options = array_merge($defaultOptions, $options);
    parent::__construct($dsn, $user, $password, $options);
  }

  // querying db with run handles preparation and execution automatically for queries with arguments
  public function run(string $sql, array $args = [])
  {
    // if no arguments exist we don't need to prepare & execute
    if (!$args) {
      return $this->query($sql);
    }
    // if arguments exist then prepare & execute
    $query = $this->prepare($sql);
    $query->execute($args);
    return $query;
  }
}
