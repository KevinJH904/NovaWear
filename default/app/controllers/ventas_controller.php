<?php

class VentasController extends AppController{
    public function index() {
        $this->subtitle = "Lista de Ventas";
        $this->ventas = (new Ventas())->find();
    }

    /*
    cliente = X (selección de cliente
    */

    public function nueva($cliente_id=null){
        $this->cliente = null;
        $this->venta =null;
        $this->AllClientes = (new Clientes())->find();

        if($cliente_id!=null){
            $this->cliente = (new Clientes())->find($cliente_id);
            $this->Vent = (new Ventas())->find_first("clientes_id = $cliente_id AND estado = 'carrito'");
            //$this->Real="";
            $bandera=false;
            if($this->Vent->estado==="carrito" and $bandera==false){
                //$this->Real="El estatus es de Carrito";
            }
            else{
                $bandera=true;
                //$this->Real="El estatus es de Finalizada, Creando nuevo carrito";
                $this->Vent->nuevo_carrito($cliente_id);
            }
            $this->venta=(new Ventas())->get_carrito($cliente_id);
            $this->AllProductos=(new Productos())->find();

        }

        $this->subtitle = "Ventas";
        if(Input::hasGet("cliente")){
            $cliente_id = Input::get("cliente");
            $this->cliente = (new Clientes())->find($cliente_id);
            $this->venta=(new Ventas())->crear($cliente_id);
            Redirect::toAction("nueva/{$cliente_id}");
        }



        if(Input::hasPost("producto_id")){
            $producto = (new Productos())->find(Input::post("producto_id"));
            $cantidad = random_int(1,10);
            $Venta = (new Ventas())->find_first("clientes_id = $cliente_id AND estado = 'carrito'");
            //$this->probar=$Venta->id;
            ///////////////////////////////////////////
//            $detalle_existente = (new detalles_ventas())->find_first("ventas_id = $Venta->id AND productos_id = {$producto->id}");
//
//            if ($detalle_existente) {
//                $this->hola="Existe el producto en los detalles";
//            }
//            else{
//                $this->hola="nel, no existe";
//            }

            ///////////////////////////////////////////

            $this->venta->add_item2($producto, $cantidad, $Venta->id);
        }
    }

    public function ver($id){
        $this->subtitle = "Venta específica";
        $this->ventas= (new Ventas())->find($id);

        $datos_ventas = Load::model('Ventas')->obtenerComparacionVentas($this->ventas=(new Ventas())->find($id));

        if (!$datos_ventas) {
            Flash::error("Venta no encontrada.");
            return Router::redirect("ventas/index"); // Redirigir si la venta no existe
        }

        $this->total_venta = $datos_ventas['total_venta'];
        $this->total_general = $datos_ventas['total_general'];

        $datos_categorias = Load::model('Ventas')->obtenerCategoriasPorVenta($this->ventas=(new Ventas())->find($id));

        if (!$datos_categorias) {
            Flash::error("No hay productos en esta venta.");
            return Router::redirect("ventas/index"); // Redirigir si no hay productos
        }

        $this->categorias = json_encode(array_keys($datos_categorias)); // Nombres de categorías
        $this->cantidades = json_encode(array_values($datos_categorias)); // Cantidades
    }

    public function registrar(){
        $this->subtitle = "Registrar Ventas";
        $this->clientes= (new Clientes())->find();
        $this->empleados= (new Empleados())->find();
        $this->metodos= (new metodospago())->find();

        if(Input::hasPost('venta')){
            $venta=new Ventas(Input::post('venta'));


            if($venta->create()){
                Flash::valid("Venta registrado");
                Input::delete();
            }else {
                Flash::error("Error al registrar venta");
            }


        }
    }
}