<?php

namespace ultimo\util\session;

abstract class SessionHandler {
  abstract public function open($savePath, $sessionName);

  abstract public function close();

  abstract public function read($id);

  abstract public function write($id, $data);

  abstract public function destroy($id);

  abstract public function gc($maxlifetime);
  
  public function register() {
    session_set_save_handler(
      array($this, 'open'),
      array($this, 'close'),
      array($this, 'read'),
      array($this, 'write'),
      array($this, 'destroy'),
      array($this, 'gc')
    );
    
    register_shutdown_function('session_write_close');
  }
}