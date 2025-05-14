<?php

class detallesVentasController extends AppController{
    public function index() {
        $this->subtitle = "Detalles de las ventas";
        $this->detallesVentas = (new detalles_ventas())->find();

        //$importe=$this->unitario * $this->cantidad;

    }
    public function ver($id){
        $this->subtitle = "Detalles de la venta específica";
        $this->detallesVentas= (new detalles_ventas())->find($id);
    }

    public function registrar(){
        $this->subtitle = "Registrar Detalles de la venta";
        $this->ventas= (new Ventas())->find();
        $this->productos= (new Productos())->find();


        if(Input::hasPost('DetVentas')){
            $DetVentas=new detalles_ventas(Input::post('DetVentas'));


            if($DetVentas->create()){
                Flash::valid("Detalles de las ventas registradas");
                Input::delete();
            }else {
                Flash::error("Error al registrar el detalle de las ventas");
            }


        }
    }
}