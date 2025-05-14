<?php

class CategoriasController extends AppController{
    public function index() {
        $this->subtitle = "Lista de Categorias";
        $this->categorias = (new Categorias())->find();
    }
    public function ver($id){
        $this->subtitle = "Categoría específica";
        $this->categorias= (new Categorias())->find($id);
        //$this->P_id=getProductos()->id;
        /////////////////////
        //$this->DET=(new detalles_ventas())->find("productos_id = $this->P_id");
        $this->CAT=(new Categorias())->find();

        //$datos = Load::model('categorias')->obtenerEstadisticasCategorias($this->CAT);#$this->DET, $this->CAT);
        //$datos2 = "hola";
        /*
        $this->categorias2 = $datos['categorias'];
        $this->ventas_por_categoria = $datos['ventas_por_categoria'];
        $this->ingresos_por_categoria = $datos['ingresos_por_categoria'];
        $this->stock_por_categoria = $datos['stock_por_categoria'];
        $this->categoria_mas_vendida = $datos['categoria_mas_vendida'];
        */

        $datos = Load::model('categorias')->obtenerDatosCategoria($this->categorias);

        //$this->categoria = $datos['categoria'];
        $this->ventas_categoria = $datos['ventas_categoria'];
        $this->ingresos_categoria = $datos['ingresos_categoria'];
        $this->stock_categoria = $datos['stock_categoria'];
        $this->ventas_otros = $datos['ventas_otros'];

        $datos_ingresos = Load::model('categorias')->obtenerComparacionIngresos($this->categorias);
        $this->ingresos_otros = $datos_ingresos['ingresos_totales'];



    }
    public function registrar(){
        $this->subtitle = "Registrar Categorias";

        if(Input::hasPost('categoria')){
            $categoria=new Categorias(Input::post('categoria'));


            if($categoria->create()){
                Flash::valid("Categoria registrado");
                Input::delete();
            }else {
                Flash::error("Error al registrar categoria");
            }


        }
    }
}