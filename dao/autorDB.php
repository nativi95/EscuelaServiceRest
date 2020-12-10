<?php 
require_once "./db/db.php";
class AutorDB
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
        try
        {
            $this->mysqliconn = BaseDatos::conectar();
        }
        catch(mysqli_sql_exception $e)
        {
            http_response_code(500);
            exit;
        }

    }

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * Metodo que recibe la accion y el metodo de comunicaicon para ejecutar un GET y direcciona las diferentes consultas segun la peticion
     */
    function GetAutores()
    {
        if($_REQUEST['action'] == 'autores')
        {
            $db = new AutorDB();
            $response = $db->GetAutoresAll();
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }

     /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * Metodo que imprime en pantalla todos los registros
     */
    public function GetAutoresAll()
    {
        $stmt = $this->mysqliconn->prepare("SELECT * FROM Autor;");
        $stmt->execute();
        $stmt->bind_result($col0,$col1,$col2);
        $autores = array();
        while($stmt->fetch())
        {
            $autores[]=['IdAutor'=>$col0, 'Nombre'=>$col1, 'Carnet'=>$col2];
        }
        $stmt->close();
        return $autores;
    }

     /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * @param id
     * Metodo que imprime en pantalla un registro segun id
     */
    function response($code = 200, $status = "", $message = "")
    {
        http_response_code($code);
        if (!empty($status) && !empty($message)) {
            $response = array("status" => $status, "message" => $message);
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }
}
?>