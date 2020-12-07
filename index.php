<?php
//Permite poder utilizar el archivo .htaccess
if (isset($_SERVER['HTTP_ORIGIN'])) {
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Max-Age: 86400'); //cache por un dia
}

//Access-Control header son recividad durante OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
    header("Access-Control-Allow-headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
  exit(0);
}

require_once "API/autorAPI.php";
require_once "API/alumnosAPI.php";

//instancias a las clases API con sus objetos
$alumnosAPI = new AlumnosAPI();
$autorAPI = new AutorAPI();

//Ejecucucion de metodo API
switch ($_REQUEST['action']) {
  case "alumnos":
    $alumnosAPI->API();
    break;
  case "autores":
    $autorAPI->API();
    break;
  default:
    $response = array("status" => "404", "message" => "Not Found");
    echo json_encode($response, JSON_PRETTY_PRINT);
  break;
}
?>