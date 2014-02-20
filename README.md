# Ultimo Session
Extensible custom session handling and modular session storage.

## Features
* Stores session values in a namespace, so values don't get mixed up between different modules.
* Custom session handling abstration
 * File session handler

## Requirements
* PHP 5.3

## Usage
### Modular session storage
	$session = new \ultimo\util\net\Session('forum');

	if (!isset($session->username)) {
		$session->username = 'Foo';
		$session->flush();
	} else {
		echo "Username already in session {$session->username}";
	}
	

### Custom session handling
	ini_set('session.save_path', __DIR__ . DIRECTORY_SEPARATOR . 'sessions');
	$handler = new \ultimo\util\session\FileSessionHandler();
	$handler->register();