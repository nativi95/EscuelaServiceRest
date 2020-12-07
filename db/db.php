<?php
//Incluimos el contenido de las constantes de conexion
require_once "config.php";

class BaseDatos
{

    private $conexion;
    private $db;

    // Creamos la cadena de conexion al servidor de bases de datos
    public static function conectar()
    {
        $conexion = mysqli_connect(host, user, pass, dbname, port);

        //Evaluamos que la conexion no nos retorne codigos de error
        if ($conexion->connect_errno) {
            die("Lo sentimos, no se ha podido conectar con MySQL/MariaDB: " . mysqli_error($conexion));
        } else {
            //Seleccionamos la base de datos a la que nos conectaremos por defecto
            $db = mysqli_select_db($conexion, dbname);
            if ($db == 0) {
                die("Lo sentimos, no se ha podido conectar con la base de datos: " . dbname);
            }
        }
        return $conexion;
    }
    public function desconectar($conexion)
    {
        if ($conexion) {
            mysqli_close($conexion);
        }
    }
}
