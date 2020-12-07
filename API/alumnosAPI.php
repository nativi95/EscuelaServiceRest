<?php
    require_once "./dao/alumnosDB.php";

    class AlumnosAPI
    {
        protected $alumnosDB;

        public function __construct()
        {
            $this->alumnosDB = new AlumnosDB();
        }

        public function API()
        {
            header('Content-Type: application/JSON');
            $method = $_SERVER['REQUEST_METHOD'];
            switch ($method) {
                case 'GET':
                    $this->alumnosDB->GetAlumnos();
                    break;
                case 'POST':
                    $this->alumnosDB->SaveAlumno();
                    break;
                case 'PUT':
                    $this->alumnosDB->UpdateAlumno();
                    break;
                case 'DELETE':
                    $this->alumnosDB->DeleteAlumno();
                    break;
                default: //metodo desconocido
                    $this->alumnosDB->response(405);
                    break;
            }
        }
    }
?>