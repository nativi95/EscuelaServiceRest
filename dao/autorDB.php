<?php 
require_once "./db/db.php";
class AutorDB
{
    protected $dbConn;
    protected $mysqliconn;

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

    function GetAutores()
    {
        if($_REQUEST['action'] == 'autores')
        {
            $db = new AutorDB();
            $response = $db->GetAutoresAll();
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }

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