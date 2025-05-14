<?php
// app/controller/alumnos_controller.php
class AlumnosController extends AppController
{
    // función (function) => acción (action)
    // /alumnos o /alumnos/index
    // app/views/alumnos/index.phtml
    public function index()
    {

    }

    // app/views/alumnos/ver.phtml
    // url => alumnos/ver/:id
    public function ver($id = 90){

        $alumnos[80] = "Ana fernanda";
        $alumnos[90] = "Ana María";
        $this->nombre = $alumnos[$id];
        // <h2> <?= $nombre ? </h2>
    }

    public function nuevo(){


    }
}