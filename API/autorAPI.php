<?php 
require_once "./dao/autorDB.php";
class AutorAPI
{
    protected $autorDB;

    public function __construct()
    {
        $this->autorDB = new AutorDB();
    }

    /**
     * @autor Juan Carlos Ruiz Nativi
     * @Carnet RN100216
     * metodo que direcciona el tipo de solicitud GET
     */
    public function API()
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method)
        {
            case 'GET':
                $this->autorDB->GetAutores();
            break;
            default://metodo NO soportado
            $this->worldDB ->response(405);
            break;
        }
    }
  
}
?>