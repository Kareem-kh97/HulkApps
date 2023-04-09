<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__.'/../vendor/autoload.php';

// require_once __DIR__.'/dao/UserDao.class.php';
// require_once __DIR__.'/dao/MovieDao.class.php';



require_once __DIR__.'/services/MovieService.class.php';
require_once __DIR__.'/services/UserService.class.php';


Flight::register('userDao', 'UserDao');
// Flight::register('movieDao', 'MovieDao');

Flight::register('userService', 'UserService');
Flight::register('movieService', 'MovieService');

// phpinfo();

Flight::map('error', function($ex){
  // Handle error
  if($user['id'] !== null) {
      // Flight::json(['message' => $ex->getMessage()], 500);
  } else {
      Flight::json(['message' => 'An error occurred.'], 500);
  }
});


/* utility function for reading query parameters from URL */
Flight::map('query', function($name, $default_value = NULL){ //$default_value if parameter is not present in the query string
  $request = Flight::request();
  $query_param = @$request->query->getData()[$name]; // getting the value of the query with the specified name using getData
  $query_param = $query_param ? $query_param : $default_value; // if not present set value to default
  return urldecode($query_param); // returns decoded value
});

// middleware method for login
Flight::route('/*', function(){
  return TRUE;
  //perform JWT decode
  $path = Flight::request()->url;
  // if($path == '/hulkApps/rest/home') Flight::json(["message" => "path is: ".$path], 403);
  if ($path == '/login' || $path == '/signup' || $path == '/docs.json' || $path == '/hulkApps/rest/home' ) return TRUE; // exclude login route from middleware
  
  $headers = getallheaders();
  if (@!$headers['Authorization']){
    Flight::json(["message" => "Authorization is missing"], 403);
    return FALSE;
  }else{
    try {
      $decoded = (array)JWT::decode($headers['Authorization'], new Key(Config::JWT_SECRET(), 'HS256'));
      Flight::set('user', $decoded);
      return TRUE;
    } catch (\Exception $e) {
      Flight::json(["message" => "Authorization token is not valid"], 403);
      return FALSE;
    }
  }
});

/* REST API documentation endpoint */
Flight::route('GET /docs.json', function(){
  $openapi = \OpenApi\scan('routes');
  header('Content-Type: application/json');
  echo $openapi->toJson();
});


require_once __DIR__.'/routes/MovieRoutes.php';
require_once __DIR__.'/routes/UserRoutes.php';

Flight::start();
?>
