<?php

class PagosController extends AppController{
    // listar los pagos realizados
    public function index(){

    }

    /*
     * /pagos/nuevo //lista a los clientes
     * /pagos/nuevo/:cliente_id //Mostrar las ventas adeudas
     */

    public function nuevo($cliente_id = null){
        $this->subtitle = "PAGOS";
        $this->cliente=null;
        $this->ventas=null;
        $this->AllClientes = (new Clientes())->find();

        if(Input::hasGet("cliente")){
            $cliente_id = Input::get("cliente");
            $this->cliente = (new Clientes())->find($cliente_id);
            Redirect::toAction("nuevo/{$cliente_id}");
        }

        if($cliente_id != null){
            $this->cliente = (new Clientes())->find($cliente_id);
        }

        if($this->cliente!==null) {
            $this->ventas = (new Ventas())->por_pagar($cliente_id);
        }
    }

    /*
     * params get ?venta[4]=60000 & venta[12]=&venta[19]=5000
     */
    public function finalizar($cliente_id = null){
        $this->cliente = (new Clientes())->find($cliente_id);
        $ventas = Input::get("ventas");
        //venta[4]=100
        $this->ventas_a_pagar = [];
        $this->total_a_abonar=0;
        foreach ($ventas as $k => $v){
            if($v !== "") {
                $item = (new Ventas())->find($k);
                $item->a_abonar = $v;
                $this->total_a_abonar += $v;
                $this->ventas_a_pagar[] =$item;
            }
        }
    }
}