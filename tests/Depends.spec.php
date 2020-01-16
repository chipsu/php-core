<?php

use metrica\core\Depends;
use metrica\core\DependsInterface;

interface DBInterface
{
}

class DB implements DBInterface
{
}

interface ControllerInterface
{
}

class Controller implements ControllerInterface
{
  public DBInterface $db;
  public function __construct(DBInterface $db)
  {
    $this->db = $db;
  }
}

describe('Depends', function() {
  beforeEach(function() {
    $this->depends = new Depends([
      'db' => DB::class,
      'controller' => Controller::class,
    ]);
  });

  describe('Testing stuff', function() {
    it('should work', function() {
      $db = $this->depends->get('db');
      assert($db instanceof DBInterface);
    });

    it('should create new instance', function() {
      $db1 = $this->depends->get('db');
      $db2 = $this->depends->create('db');
      assert($db1 !== $db2);
    });

    it('should work with nested dependencies', function() {
      $controller = $this->depends->get('controller');
      assert($controller instanceof ControllerInterface);
      assert($controller->db instanceof DBInterface);
    });

    it('should share nested dependencies', function() {
      $controller1 = $this->depends->get('controller');
      $controller2 = $this->depends->create('controller');
      assert($controller1 !== $controller2);
      assert($controller1->db === $controller2->db);
    });
  });

  describe('Testing closure constructors', function() {
    it('should work', function() {
      $this->depends->addDependencies([
        'db2' => fn() => new DB(),
        'controller2' => fn(DBInterface $db2) => new Controller($db2),
      ]);
      $controller = $this->depends->get('controller2');
      assert($controller instanceof ControllerInterface);
      assert($controller->db instanceof DBInterface);
    });
  });
});
