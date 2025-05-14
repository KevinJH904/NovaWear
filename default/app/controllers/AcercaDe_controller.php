<?php
// app/controller/alumnos_controller.php
class AcercaDeController extends AppController
{
    // función (function) => acción (action)
    // /alumnos o /alumnos/index
    // app/views/alumnos/index.phtml
    public function index()
    {
        $this->title = "";
        $this->subtitle = "Información acerca de mí";

    }
}