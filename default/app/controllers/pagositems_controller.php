<?php

class PagositemsController extends AppController{
    // listar los pagos realizados
    public function index(){
        $this->pagositems= (new Pagositems())->find();
        $this->subtitle="";

    }

    /*
     * /pagos/nuevo //lista a los clientes
     * /pagos/nuevo/:cliente_id //Mostrar las ventas adeudas
     */

    public function nuevo($cliente_id = null){

    }

    /*
     * params get ?venta[4]=60000 & venta[12]=&venta[19]=5000
     */
    public function finalizar($cliente_id = null){

    }
}