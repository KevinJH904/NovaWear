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
        //Esto pasa antes de que se haya entradoa nueva/id
        $this->ultimaVenta = (new Ventas())->find_first("order: id DESC");
        $this->cliente = null;
        $this->venta =null;
        $this->AllClientes = (new Clientes())->find();
        $this->VentAllCarrito = (new Clientes())->find("id IN (SELECT clientes_id FROM ventas WHERE estado = 'carrito')");

        if($cliente_id!=null){
            //Esto pasa cuando YA SE ENTRO a nueva/id
            $this->cliente = (new Clientes())->find($cliente_id);
            $this->Vent = (new Ventas())->find_first("clientes_id = $cliente_id AND estado = 'carrito'");
            //$this->Real="";
            $this->totalTemp = round((new detalles_ventas())->sum("importe", "conditions: ventas_id = {$this->Vent->id}"), 2);

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

        $this->subtitle = "Carrito de Compras";
        //funcion que se ocupa para elegir a un cliente en /nueva
        if(Input::hasGet("cliente")){
            $cliente_id = Input::get("cliente");
            $this->cliente = (new Clientes())->find($cliente_id);
            $this->venta=(new Ventas())->crear($cliente_id);
            Redirect::toAction("nueva/{$cliente_id}");
        }
        //función que se ocupar para meter nuevos productos al carrito
        if(Input::hasPost("producto_id")){
            $producto = (new Productos())->find(Input::post("producto_id"));
            $cantidad = Input::post("cantidad");
            $Venta = (new Ventas())->find_first("clientes_id = $cliente_id AND estado = 'carrito'");
            $Client = (new Clientes())->find($cliente_id);


            if(Input::post("accion")== "agregar"){
                //cantidad se debera cambiar por el campo nuevo
                $this->venta->add_item2($producto, $cantidad, $Venta->id);
                $this->venta->set_total();
                Redirect::toAction("nueva/$cliente_id");
            }
            elseif(Input::post("accion")== "eliminar"){
                $this->venta->remove_item2($producto, $cantidad, $Venta->id);
                $this->venta->set_total();
                Redirect::toAction("nueva/$cliente_id");
            }
            elseif (Input::post("accion")== "pagar"){
                if ($Client->credito >= $this->totalTemp) {
                    $this->venta->set_finalizar();
                    Redirect::to("pagos/nuevo/$cliente_id");
                } else {
                    Flash::error("El crédito disponible del cliente ($$Client->credito) es menor al total a pagar ($$this->totalTemp). No se puede proceder al pago.");
                    Redirect::to("ventas/nueva/$cliente_id");
                }
            }
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
        $this->fecha = date("Y-m-d");

        if(Input::hasPost('venta')){
            $datos = Input::post('venta');
            if (!$datos) {
                Flash::error("No se recibieron datos del formulario.");
                return;
            }

            $venta = new Ventas();
            $venta->clientes_id = $datos['clientes_id'] ?? null;
            $venta->empleados_id = $datos['empleados_id'] ?? null;
            $venta->usuario_id = $datos['usuario_id'] ?? null;
            $venta->metodos_pago_id = $datos['metodos_pago_id'] ?? null;
            $venta->fecha = $datos['fecha'] ?? date('Y-m-d');
            $venta->total = $datos['total'] ?? 0;
            $venta->por_pagar = $datos['por_pagar'] ?? 0;
            $venta->comentario = $datos['comentario'] ?? null;
            $venta->cancelada = $datos['cancelada'] ?? 0;
            $venta->estado = 'carrito';

            $venta->forma_pago = ($venta->metodos_pago_id == 8) ? 'PPD' : 'PUE';

            if($venta->create()){
                Flash::valid("Venta registrado");
                Input::delete();

                // Obtener el ID de la venta recién creada
                $venta_id = $venta->id;

                // Obtener el cliente asociado a esta venta
                $venta_completa = (new Ventas())->find($venta_id);
                $cliente_ids = $venta_completa->clientes_id;

                Redirect::toAction("nueva/$cliente_ids");

            }else {
                Flash::error("Error al registrar venta");
            }


        }
    }
}