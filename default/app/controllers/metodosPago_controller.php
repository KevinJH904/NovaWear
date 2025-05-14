<?php

class metodosPagoController extends AppController{
    public function index() {
        $this->subtitle="Lista de Metodos de Pago";
        $this->metodosPago = (new metodospago())->find();
    }
    public function ver($id){
        $this->subtitle="Metodo de Pago específico";
        $this->metodosPago= (new metodospago())->find($id);
        //$ventas = Load::model('ventas')->find("metodos_pago_id=$id");
        //$this->ventasMetodo = Load::model('metodos_pago')->TotalMes();
        $datos_ventas  = Load::model('metodospago')->obtenerVentasPorMetodo((new metodospago())->find($id));

        $this->ventas_especifico = $datos_ventas['ventas_especifico'];
        $this->total_ventas = $datos_ventas['total_ventas'];


        $datos_ingresos = Load::model('MetodosPago')->obtenerIngresosPorMetodo($this->metodosPago);
        $this->ingresos_metodo = $datos_ingresos['ingresos_metodo'];
        $this->ingresos_totales = $datos_ingresos['ingresos_totales'];


        $datos_ventas = Load::model('MetodosPago')->obtenerVentasPorMetodoEspecifico($this->metodosPago);
        $this->ventas_detalladas = $datos_ventas['ventas_detalladas'];



    }

    public function registrar(){
        $this->subtitle="Registrar Metodo de Pago";

        if(Input::hasPost('metodo')){
            $metodo=new metodospago(Input::post('metodo'));


            if($metodo->create()){
                Flash::valid("Metodo registrado");
                Input::delete();
            }else {
                Flash::error("Error al registrar Metodo");
            }


        }
    }
}