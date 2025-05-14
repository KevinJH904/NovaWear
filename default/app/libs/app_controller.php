<?php
/**
 * @see Controller nuevo controller
 */
require_once CORE_PATH . 'kumbia/controller.php';

/**
 * Controlador principal que heredan los controladores
 *
 * Todos las controladores heredan de esta clase en un nivel superior
 * por lo tanto los métodos aquií definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 */
abstract class AppController extends Controller
{

    final protected function initialize() {
        if (!Auth::is_valid()) {
            // El usuario no es valido, lo mandamos al login
            Redirect::to("login");
            return false;
        }
        else{
            View::template("adminlte");
        }
    }

//    final protected function initialize()
//    {
//        $this -> title = "Web 20251";
//        View::template("adminlte");
//    }

    final protected function finalize()
    {
        
    }

}
