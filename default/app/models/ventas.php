<?php
class Ventas extends ActiveRecord{

    public function initialize() {
        /*     * Nombre de la relacion
         * Nombre de la clase del modelo con quien se relaciona     * Nombre de la columna de relacion
         * Para llamar a la relación
         * -> getVendedor()
         * -> getmetodoP()
         * -> getcliente()
         * -> getdetalleventa()
         */

        $this->belongs_to('cliente', 'model: Clientes', 'fk: clientes_id');
        $this->belongs_to('vendedor', 'model: empleados', 'fk: empleados_id');
        $this->belongs_to('metodoP', 'model: metodospago', 'fk: metodos_pago_id');
        $this->has_many('detalleventa', 'model: detalles_ventas', 'fk: ventas_id');
        $this->has_many('items', 'model: pagositems', 'fk: venta_id');
        $this->has_many('pagosC', 'model: pagos', 'fk: ventass_id');
        $this->has_many('pagosM', 'model: pagos', 'fk: metodo_pago_id');



        //Validaciones
        $this->validates_presence_of("clientes_id"); // El campo no puede ser nulo
        $this->validates_presence_of("empleados_id"); // El campo no puede ser nulo
        $this->validates_presence_of("metodos_pago_id"); // El campo no puede ser nulo
        $this->validates_presence_of("fecha"); // El campo no puede ser nulo
//        $this->validates_presence_of("total"); // El campo no puede ser nulo
//        $this->validates_presence_of("por_pagar"); // El campo no puede ser nulo
        $this->validates_presence_of("comentario"); // El campo no puede ser nulo
//        $this->validates_presence_of("cancelada"); // El campo no puede ser nulo

        //Rangos
        $this->validates_length_of('cancelada', '1');
        $this->validates_length_of('comentario', '80', '1');

        //$this->validates_date_in("fecha");


    }

    public function get_carrito($cliente_id){
        return (new Ventas())->find("conditions: clientes_id = {$cliente_id} AND estado = 'carrito'")[0];
    }

    public function nuevo_carrito($cliente_id){

        $sql = "INSERT INTO ventas (clientes_id, empleados_id, metodos_pago_id, fecha, total, por_pagar, comentario, forma_pago, cancelada, estado) VALUES ($cliente_id, 1, 1, '2025-04-09', 0, 0, '', '', 0, 'carrito')";

        (new Ventas())->sql($sql);

    }

    public function crear($cliente_id){
        $venta = (new Ventas());
        $venta->cliente_id = $cliente_id;
        $venta->estado = "carrito";
        $venta->save();

        return $venta;
    }

    public function add_item2($producto, $cantidad, $venta_id) {
        if ($this->estado === "carrito") {
            // Verificar si ya existe ese producto en el carrito
            $detalle_existente = (new detalles_ventas())->find_first("ventas_id = $venta_id AND productos_id = {$producto->id}");

            if ($detalle_existente) {
                //$this->hola="Existe ya una venta con ese producto";
                // Si ya existe, incrementamos la cantidad en 1
                $detalles = new detalles_ventas();
                $sql = "UPDATE detalles_ventas 
                        SET cantidad = cantidad + $cantidad, 
                            importe = (cantidad) * unitario 
                        WHERE ventas_id = $venta_id AND productos_id = {$producto->id}";
                $detalles->sql($sql);
                $producto->bajastock($producto->id, $cantidad, 1);
            } else {
                // Si no existe, creamos uno nuevo
                $item = new detalles_ventas();
                $item->ventas_id = $venta_id;
                $item->productos_id = $producto->id;
                $item->cantidad = $cantidad;
                $item->unitario = $producto->precio;
                $item->save();

                // Actualizar el stock solo en nuevo item
                $producto->bajastock($item->productos_id, $item->cantidad, 1);
            }
        }
    }

    public function remove_item2($producto, $cantidad, $venta_id) {
        if ($this->estado === "carrito") {
            // Verificar si ya existe ese producto en el carrito
            $detalle_existente = (new detalles_ventas())->find_first("ventas_id = $venta_id AND productos_id = {$producto->id}");

            if ($detalle_existente) {
                //$this->hola="Existe ya una venta con ese producto";
                // Si ya existe, incrementamos la cantidad en 1
                $detalles = new detalles_ventas();
                $sql = "UPDATE detalles_ventas 
                    SET cantidad = GREATEST(cantidad - $cantidad, 0), 
                        importe = GREATEST(cantidad - $cantidad, 0) * unitario 
                    WHERE ventas_id = $venta_id AND productos_id = {$producto->id}";
                $detalles->sql($sql);
                $producto->bajastock($producto->id, $cantidad, 0);
            }
        }
    }

    public function add_item($producto, $cantidad, $venta_id){
        //Algoritmo para la actualización de datos
        if($this->estado==="carrito"){
            $item =(new detalles_ventas());
            $item->ventas_id = $this->id;
            $item->productos_id = $producto->id;
            $item->cantidad = $cantidad;
            $item->unitario=$producto->precio;
            $item->save();
            //$detalle=$this->getDetalleventa();
            //$detalle->getProducto()->bajastock($producto->id, $cantidad);
            // Habilitar la siguiente línea para actualizar el stock
            $producto->bajastock($item->productos_id, $item->cantidad);
        }
        if($this->estado === "finalizada"){
            //$this->getCliente()->linea_credito();
        }




        //$venta = (new Ventas())->find(19);
        //$venta->save();
        //$producto = (new Productos())->find(random_int(1,9));
        //$items=(new detalles_ventas()) ;

        //$item->ventas_id=$venta->id;
        //$item==$producto->nombre;
        $item="";
        //$this->descripcion="";
        //$this->unitario ="";
        //$this->subtotal="";
        //$this->importe="";

    }

    public function set_finalizar(){
        //if($this->estado==="carrito"){
            $this->activo = false;
            $this->estado="finalizada";
            //if de la foto
            if ($this->forma_pago === "PPD") {
                $this->por_pagar = $this->total;
            }

            //quitar esta linea para que vuelva a la normalidad por se forma_pago PPD
            //$this->por_pagar = $this->total;

            //guardar
            $this->save();
            //Se quita set_total ya que ya lo ocupamos desde indexcontroller al usar pagar()
            $this->set_total();
            $this->getCliente()->update_credito();
            $this->getCliente()->linea_credito();
        //}



    }

    //Valida que el crédito sea suficiente
    // PUE: Contado y PPD: Crédito
    public function venta_valida(){
        if($this->forma_pago === "PPD"){
            $credito_suficiente = ($this->total < $this->getCliente()->credito) ? 1 : 0;
            return $credito_suficiente;
        }
        return true;
    }

    public function set_total(){
        $this->total = (new detalles_ventas())->sum("importe", "conditions: ventas_id = {$this->id}");
        $this->save();
    }

    public function pagar(){
        if ($this->venta_valida() === 1) {
            // Lógica si es válida
            $this->set_total();
        } else {
            // Lógica si no es válida
            return false;
        }

    }

    /*
     * SELECT * FROM ventas
     * WHERE cliente_id = 9
     * AND por_pagar > 0
     * AND STATUS = 'finalizada'
     */

    //clientes que le falta pagar goty para la seleccion de usuarios
    public function por_pagar($cliente_id){
        return (new Ventas())->find("clientes_id = {$cliente_id}
                                   AND por_pagar > 0
                                   AND estado = 'finalizada'");
    }

    public function obtenerComparacionVentas($venta, $total=0) {
        // Obtener la venta específica
        //$venta = $this->find_first((int) $venta_id);

        if (!$venta) {
            return null; // Si no existe la venta, retornamos null
        }

        // Obtener el total de la venta específica
        $total_venta = $venta->total;

        // Obtener la suma de todas las ventas
        $total_general = $this->sum("total");

        return [
            'total_venta' => $total_venta,
            'total_general' => $total_general
        ];
    }

    public function obtenerCategoriasPorVenta($venta_id) {
        // Obtener los detalles de la venta
        //$detalles_venta = Load::model('detalles_ventas')->find("ventas_id = $venta_id");
        $detalles_venta = $venta_id->getDetalleventa(); // Relación con detalles_ventas


        if (!$detalles_venta) {
            return null; // Si no hay detalles, retornamos null
        }

        // Array para contar la cantidad de productos por categoría
        $categorias_cantidad = [];

        foreach ($detalles_venta as $detalle) {
            $producto = $detalle->getProducto(); // Obtener el producto
            $categoria = $producto->getCategoria(); // Obtener la categoría del producto

            if ($categoria) {
                $nombre_categoria = $categoria->nombre; // Nombre de la categoría

                // Si la categoría ya existe en el array, sumamos la cantidad
                if (isset($categorias_cantidad[$nombre_categoria])) {
                    $categorias_cantidad[$nombre_categoria] += $detalle->cantidad;
                } else {
                    $categorias_cantidad[$nombre_categoria] = $detalle->cantidad;
                }
            }
        }

        return $categorias_cantidad; // Retornamos las categorías con sus cantidades
    }

}
