<?php

namespace ultimo\util\net;

class Session {
  const SESSION_KEY = '__SessionStorage';
  
  /**
   * The namespace of the session.
   * @var string
   */
  protected $namespace;
  
  /**
   * The session data. A hashtable with variable names as key and variable
   * values as value.
   * @var array
   */
  protected $data;
  
  /**
   * Footprint of the data to determine whether a write is needed.
   * @var string
   */
  protected $footprint;
  
  /**
   * Constructor.
   * @param string $namespace The namespace of the session.
   */
  public function __construct($namespace) {
    $this->namespace = $namespace;
    $this->initData();
  }
  
  /**
   * Fetches the session data of the namespace and stores it in a member of this
   * class. It also makes sure an entry for the data of this namespace exists in
   * the session.
   */
  protected function initData() {
    $this->sessionStart();
    
    if (!array_key_exists(self::SESSION_KEY, $_SESSION)) {
      $_SESSION[self::SESSION_KEY] = array();
    }
    
    if (!array_key_exists($this->namespace, $_SESSION[self::SESSION_KEY])) {
      $_SESSION[self::SESSION_KEY][$this->namespace] = array();
    }
    
    $this->data = &$_SESSION[self::SESSION_KEY][$this->namespace];
    $this->sessionWriteClose();
    $this->footprint = $this->calculateFootprint();
  }
  
  /**
   * Calculates the footprint of the session data.
   * @return string Footprint of the session data.
   */
  protected function calculateFootprint() {
    return sha1(serialize($this->data));
  }
  
  /**
   * Sets a variable in the session.
   * @param string $key The name of the variable to set.
   * @param mixed $value The value of the variable to set.
   */
  public function __set($key, $value) {
    $this->data[$key] = $value;
  }
  
  /**
   * Returns the value of a variable in the session.
   * @param string $key The name of the variable to get.
   * @return mixed The value of the variable or null if the variable does not
   * exist.
   */
  public function __get($key)  {
    if (!array_key_exists($key, $this->data)) {
      return null;
    }
    
    return $this->data[$key];
  }
  
  /**
   * Returns whether a session variable exists.
   * @param string $key The name of the variable.
   * @return boolean Whether the variable exists.
   */
  public function __isset($key) {
    return array_key_exists($key, $this->data);
  }
  
  /**
   * Unsets a variable in the session.
   * @param string $key The name of the variable to unset.
   */
  public function __unset($key) {
    unset($this->data[$key]);
  }
  
  /**
   * Writes the session data.
   */
  public function flush() {
    $currentFootprint = $this->calculateFootprint();
    if ($this->footprint != $currentFootprint) {
      $this->sessionStart();
      $_SESSION[self::SESSION_KEY][$this->namespace] = $this->data;
      $this->sessionWriteClose();
      $this->footprint = $currentFootprint;
    }
  }
  
  /**
   * Retruns the namespace of the session.
   * @return string The namespace of the session.
   */
  public function getNamespace() {
    return $this->namespace;
  }
  
  /**
   * Returns all session data.
   * @return array All session data.
   */
  public function getData() {
    return $this->data;
  }
  
  /**
   * Erases the session data.
   */
  public function reset() {
    $this->data = array();
  }
  
  /**
   * Starts the PHP session.
   */
  protected function sessionStart() {
    try {
      session_start();
    } catch (LogicException $e) { } // the session could already be started
  }
  
  /**
   * Write closes the PHP session.
   */
  protected function sessionWriteClose() {
    session_write_close();
  }
  
  
}