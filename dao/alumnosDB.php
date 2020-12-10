<?php
require_once "./db/db.php";

class AlumnosDB
{
    
    protected $dbConn;
    protected $mysqliconn;

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * Metodo constructor que crea una conexion
     */
    public function __construct()
    {
        try {
            $this->mysqliconn = BaseDatos::conectar();
        } catch (mysqli_sql_exception $e) {
            http_response_code(500);
            exit;
        }
    }
    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++GetAlumnos all y ById

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * Metodo que recibe la accion y el metodo de comunicaicon para ejecutar un GET y direcciona las diferentes consultas segun la peticion
     */
    public function GetAlumnos()
    {
        if ($_REQUEST['action'] == 'alumnos') {
            $db = new AlumnosDB();
            if (isset($_REQUEST['id'])) {
                $response = $db->GetAlumno($_REQUEST['id']);
                echo json_encode($response, JSON_PRETTY_PRINT);
            } else {
                $response = $db->GetAlumnosAll();
                echo json_encode($response, JSON_PRETTY_PRINT);
            }
        } else {
            $this->response(400);
        }
    }

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * Metodo que imprime en pantalla todos los registros
     */
    function GetAlumnosAll()
    {
        $stmt = $this->mysqliconn->prepare("SELECT * FROM Alumnos;");
        $stmt->execute();
        $stmt->bind_result($col0, $col1, $col2, $col3);
        $alumnos = array();
        while ($stmt->fetch()) {
            $alumnos[] = ['IdAlumno' => $col0, 'Nombre' => $col1, 'Apellidos' => $col2, 'Carnet' => $col3];
        }
        $stmt->close();
        return $alumnos;
    }

     /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * @param id
     * Metodo que imprime en pantalla un registro segun id
     */
    function GetAlumno($id = 0)
    {
        $stmt = $this->mysqliconn->prepare("SELECT * FROM Alumnos WHERE IdAlumno=?;"); //se utiliza statement para protegerse de sqlinyection
        $stmt->bind_param('i', $id); // s string. i int, d double
        $stmt->execute();

        $stmt->bind_result($col0, $col1, $col2, $col3);
        $alumno = array();
        while ($stmt->fetch()) {
            $alumno[] = ['IdAlumno' => $col0, 'Nombre' => $col1, 'Apellidos' => $col2, 'Carnet' => $col3];
        }
        $stmt->close();

        return $alumno;
    }

    //===========================================================SaveAlumno

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * Metodo que recibe la accion y el metodo de comunicaicon para ejecutar un POST y guardar un nuevo registro de alumnos
     */

    function SaveAlumno()
    {
        if ($_REQUEST['action'] == 'alumnos') {
            //decodificar un string de JSON
            $obj = json_decode(file_get_contents('php://input'));
            $objArr = (array)$obj;

            if (empty($objArr)) {
                $this->response(422, "error", "Nada que añadir. Comprobar JSON");
            } else if (isset($obj->Nombre)) {
                $alumno = new AlumnosDB();
                $alumno->Insert($obj->Nombre, $obj->Apellidos, $obj->Carnet);
                $this->response(200, "success", "El nuevo alumno fue agregado");
            } else {
                $this->response(422, "error", "La pripiedad no esta definida");
            }
        } else {
            $this->response(400);
        }
    }

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * @param Nombre nombre de alumno
     *  @param Apellidos apellidos de alumno
     *  @param Carnet carnet de alumno
     * Metodo que recibe los datos para realizar un insert alumnos
     * @return retorna el resultado de la peticion sql
     */
    public function Insert($Nombres, $Apellidos, $Carnet)
    {
        $stmt = $this->mysqliconn->prepare("INSERT INTO Alumnos(Nombre, Apellidos, Carnet) VALUES(?,?,?);");
        $stmt->bind_param('sss', $Nombres, $Apellidos, $Carnet);
        $r = $stmt->execute();
        $stmt->close();
        return $r;
    }

    // /////////////////////////////////////////////////////////////UpdateAlumnos/////////////////////////////////////////////////////////

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * Metodo que recibe la accion y el metodo de comunicaicon para ejecutar un PUT y actualizar un registro de alumnos
     */
    function UpdateAlumno()
    {
        if (isset($_REQUEST['action']) && isset($_REQUEST['id'])) {
            if ($_REQUEST['action'] == 'alumnos') {
                $obj = json_decode(file_get_contents('php://input'));
                $objArr = (array)$obj;
                if (empty($objArr)) {
                    $this->response(422, "error", "Nada que actualizar. Comprobar JSON");
                } else if (isset($obj->Nombre)) {
                    $db = new AlumnosDB();
                    $db->Update($_REQUEST['id'], $obj->Nombre, $obj->Apellidos, $obj->Carnet);
                    $this->response(200, "success", "Alumnos actualizado");
                } else {
                    $this->response(422, "error", "La propiedad no esta definida");
                }
                exit;
            }
        }
        $this->response(400);
    }

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * @param id id del alumno
     * * @param Nombre nombre de alumno
     *  @param Apellidos apellidos de alumno
     *  @param Carnet carnet de alumno
     * Metodo que recibe los datos para realizar un UPDATE de alumnos
     * @return retorna el resultado de la peticion sql
     */
    public function Update($id, $Nombre, $Apellidos, $Carnet)
    {
        if ($this->CheckID($id)) {
            $stmt = $this->mysqliconn->prepare("UPDATE Alumnos SET Nombre=?, Apellidos=?, Carnet=? WHERE IdAlumno=?;");
            $stmt->bind_param('sssi', $Nombre, $Apellidos, $Carnet, $id);
            $r = $stmt->execute();
            $stmt->close();
            return $r;
        }
    }

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * @param id id del alumnos
     * Metodo que evalua la existencia de un ID
     */
    public function CheckID($id)
    {
        $stmt = $this->mysqliconn->prepare("SELECT * FROM Alumnos WHERE IdAlumno=?;");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                return true;
            }
        }
        return false;
    }

    // -----------------------------------------------------------------DeleteAlumno-------------------------------

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * Metodo que recibe la accion y el metodo de comunicaicon para ejecutar un DELETE y elimina un registro de alumnos
     */
    function DeleteAlumno()
    {
        if (isset($_REQUEST['action']) && isset($_REQUEST['id'])) {
            if ($_REQUEST['action'] == 'alumnos') {
                $db = new AlumnosDB();
                $db->Delete($_REQUEST['id']);
                exit;
            }
        }
        $this->response(400);
    }

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * @param id id del alumno
     * Metodo que recibe los datos para realizar un delete alumnos
     * @return retorna el resultado de la peticion sql
     */
    public function Delete($id = 0)
    {
        $stmt = $this->mysqliconn->prepare("DELETE FROM Alumnos WHERE IdAlumno=?;");
        $stmt->bind_param('i', $id);
        $r = $stmt->execute();
        $stmt->close();
        return $r;
    }

    // ************************************************************Mensajes*******************************************************
    //Método para generar los codigos de respuesta

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * Metodo que imprime el jscon el codigo establecido el status y un mensaje
     */
    public function response($code = 200, $status = "", $message = "")
    {
        http_response_code($code);
        if (!empty($status) && !empty($message)) {
            $response = array("status" => $status, "message" => $message);
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }
}
