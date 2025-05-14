<?php

class ProductosController extends AppController{
    public function index() {
        $this->subtitle = "Lista de productos";
        $this->productos = (new Productos())->find();
    }
    public function ver($id){
        $this -> subtitle = "Información general del Producto";

        $this->productos= (new Productos())->find($id);
        $this->EST = $this->productos->obtenerEst((new detalles_ventas())->find("productos_id=$id"));
        $this->Tot = Load::model('productos')->TotalVent((new detalles_ventas())->find());
        $this->ventas_por_mes = Load::model('productos')->TotalMes((new detalles_ventas())->find("productos_id=$id"));
        //$this->EST = "arriba las chivas";
    }
    public function registrar(){
        $this->subtitle = "Registrar Productos";
        $this->cats = (new Categorias())->find();
        if(Input::hasPost('producto')){
            $producto=new Productos(Input::post('producto'));

            if($producto->create()){
                Flash::valid("Producto registrado");
                Input::delete();
            }else {
                Flash::error("Error al registrar producto");
            }
        }
    }
}