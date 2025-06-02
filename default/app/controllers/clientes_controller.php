<?php

class ClientesController extends AppController{
    public function index() {
        $this -> subtitle = "Lista de Clientes";

        $this->clientes = (new Clientes())->find();
    }
    public function ver($id){
        $this -> subtitle = "Lista de Clientes";
        
        $this->clientes= (new Clientes())->find($id);

        /////////////////////////////////////////////
        /*
        $datos = $this->clientes->obtenerEstadisticasClientes((new detalles_ventas())->find("ventas_id = $venta->id"),$this->clientes);
        $this->clientes = $datos['clientes'];
        $this->compras_por_cliente = $datos['compras_por_cliente'];
        $this->gasto_por_cliente = $datos['gasto_por_cliente'];
        $this->clientes_por_mes = $datos['clientes_por_mes'];
        $this->cliente_mas_compras = $datos['cliente_mas_compras'];
        $this->cliente_mayor_gasto = $datos['cliente_mayor_gasto'];
        */

        $datos = Load::model('clientes')->obtenerComprasCliente($this->clientes);

        $this->cliente = $datos['cliente'];
        $this->compras = $datos['compras'];

        $datos_categoria = Load::model('clientes')->obtenerCategoriaPreferida($this->clientes);

        $this->cliente_cat = $datos_categoria['cliente'];
        $this->categoria_preferida = $datos_categoria['categoria_preferida'];

        $datos_metodos = Load::model('clientes')->obtenerMetodosPagoCliente($this->clientes);

        $this->cliente_met = $datos_metodos['cliente'];
        $this->metodos_pago = $datos_metodos['metodos_pago'];

        $datos_gasto = Load::model('clientes')->obtenerGastoMensual($this->clientes);


        $this->gasto_mensual = $datos_gasto['gasto_mensual'];
    }

    public function registrar(){
        $this->subtitle = "Registrar Clientes";

        if(Input::hasPost('cliente')){
            $cliente=new Clientes(Input::post('cliente'));

            if($cliente->create()){
                Flash::valid("Cliente registrado");
                Input::delete();
            }else {
                Flash::error("Error al registrar cliente");
            }


        }
    }

}