<?php
class Clientes extends ActiveRecord{
    public function initialize() {
        /*     * Nombre de la relacion
         * Nombre de la clase del modelo con quien se relaciona     * Nombre de la columna de relacion
         * Para llamar a la relación
         * -> getVentas
         */
        $this->has_many('ventas', 'model: Ventas', 'fk: clientes_id');

        //Validaciones
        $this->validates_presence_of("nombre"); // El campo no puede ser nulo
        $this->validates_presence_of("email"); // El campo no puede ser nulo
        $this->validates_presence_of("telefono"); // El campo no puede ser nulo
        $this->validates_presence_of("password"); // El campo no puede ser nulo

        //Rangos
        $this->validates_length_of('nombre', '30', '2');
        $this->validates_length_of('telefono', '10', '10');
        $this->validates_length_of('password', '40', '8');

        $this->validates_email_in("email");

        $this->validates_numericality_of("credito");
        $this->validates_numericality_of("adeudo");
        $this->validates_numericality_of("activo");

    }

    public function limpiarAdeudo(){
         //quitar adeudo
        $sql3= "UPDATE clientes SET adeudo = 0";
        (new Clientes())->sql($sql3);
    }

    public function linea_credito(){
        //return ($this->credito +$this->adeudo);
        //$sql2="UPDATE clientes SET credito = credito - adeudo";
        //Resta del adeudo al crédito
        $sql2= "UPDATE clientes SET credito = GREATEST(0, credito - adeudo)";

        (new Clientes())->sql($sql2);

        $this->limpiarAdeudo();

    }

    public function update_credito(){
        //$this->id
        $sql= "UPDATE clientes c SET adeudo = (SELECT SUM(por_pagar) FROM ventas v WHERE v.clientes_id = c.id AND por_pagar > 0 AND estado = 'finalizada') WHERE c.id = {$this->id}";

        (new Clientes())->sql($sql);
    }


    public function obtenerEstadisticasClientes($detalles,$clientes) {
        // Obtener todos los clientes
        //$clientes = $this->find();

        // Arrays para almacenar datos
        $compras_por_cliente = [];
        $gasto_por_cliente = [];
        $clientes_por_mes = array_fill(1, 12, 0); // Inicializar array de 12 meses con 0

        // Variables para encontrar clientes destacados
        $cliente_mas_compras = null;
        $cliente_mayor_gasto = null;
        $max_compras = 0;
        $max_gasto = 0;

        foreach ($clientes as $cliente) {
            $compras_por_cliente[$cliente->id] = 0;
            $gasto_por_cliente[$cliente->id] = 0;

            // Contar nuevos clientes por mes
            $mes_registro = (int) date('m', strtotime($cliente->created_at));
            $clientes_por_mes[$mes_registro]++;

            // Obtener compras del cliente
            $ventas = Load::model('ventas')->find("clientes_id = $cliente->id");

            foreach ($ventas as $venta) {
                $compras_por_cliente[$cliente->id]++;

                // Obtener detalles de venta y calcular el gasto total
                //$detalles = Load::model('detalles_ventas')->find("ventas_id = $venta->id");
                foreach ($detalles as $detalle) {
                    $gasto_por_cliente[$cliente->id] += $detalle->subtotal;
                }
            }

            // Encontrar el cliente con más compras
            if ($compras_por_cliente[$cliente->id] > $max_compras) {
                $max_compras = $compras_por_cliente[$cliente->id];
                $cliente_mas_compras = $cliente->nombre;
            }

            // Encontrar el cliente con mayor gasto total
            if ($gasto_por_cliente[$cliente->id] > $max_gasto) {
                $max_gasto = $gasto_por_cliente[$cliente->id];
                $cliente_mayor_gasto = $cliente->nombre;
            }
        }

        return [
            'clientes' => $clientes,
            'compras_por_cliente' => $compras_por_cliente,
            'gasto_por_cliente' => $gasto_por_cliente,
            'clientes_por_mes' => $clientes_por_mes,
            'cliente_mas_compras' => $cliente_mas_compras,
            'cliente_mayor_gasto' => $cliente_mayor_gasto
        ];
    }


    public function obtenerComprasCliente($clientes) {
        // Obtener el cliente específico
        //$cliente = $this->find_first((int) $cliente_id);
        if (!$clientes) {
            return null; // Si el cliente no existe, retornamos null
        }

        // Obtener todas las ventas realizadas por el cliente
        //$ventas = Load::model('ventas')->find("clientes_id = $clientes->id");
        $ventas = $clientes->getVentas();

        // Lista de compras del cliente
        $compras = [];
        $total = 0;
        $NoCompras = 0;

        foreach ($ventas as $venta) {
            // Obtener información del empleado que atendió la venta
            //$empleado = Load::model('empleados')->find_first($venta->empleados_id);
            $empleado = $venta->getVendedor();
            $empleado_nombre = $empleado ? $empleado->nombre : "No registrado";

            // Calcular subtotal de la venta sumando detalles de venta
            $subtotal = 0;
            $detalles_ventas = $venta->getDetalleventa(); // Relación con detalles_ventas
            foreach ($detalles_ventas as $detalle) {
                $subtotal += $detalle->importe;
                $total += $detalle->importe;
            }

            // Obtener información del método de pago
            //$metodo_pago = Load::model('metodos_pago')->find_first($venta->metodos_pago_id);
            $metodo_pago = $venta->getMetodoP(); // Método de pago usado
            $metodo_pago_nombre = $metodo_pago ? $metodo_pago->nombre : "No especificado";

            $NoCompras+=1;

            // Guardar la compra en la lista
            $compras[] = [
                'venta_id' => $venta->id,
                'empleado' => $empleado ? $empleado->nombre : "No registrado",
                'metodo_pago' => $metodo_pago ? $metodo_pago->nombre : "No especificado",
                'fecha' => $venta->fecha,
                'subtotal' => $subtotal,
                'total' => $total,
                'NoCompras' => $NoCompras
            ];
        }

        return [
            'cliente' => $clientes,
            'compras' => $compras
        ];
    }

    public function obtenerCategoriaPreferida($clientes) {
        // Obtener el cliente específico
        //$cliente = $this->find_first((int) $cliente_id);

        // Obtener todas las ventas del cliente
        $ventas = $clientes->getVentas();

        // Array para contar las compras por categoría
        $categorias_compras = [];

        foreach ($ventas as $venta) {
            // Obtener los detalles de cada venta
            $detalles_ventas = $venta->getDetalleventa();

            foreach ($detalles_ventas as $detalle) {
                // Obtener el producto de la venta
                //$producto = Load::model('productos')->find_first($detalle->productos_id);
                $producto = $detalle->getProducto();
                if ($producto) {
                    $categoria = $producto->getCategoria(); // Relación con Categoría
                    if ($categoria) {
                        $categoria_id = $categoria->id;
                        if (!isset($categorias_compras[$categoria_id])) {
                            $categorias_compras[$categoria_id] = [
                                'nombre' => $categoria->nombre,
                                'total' => 0
                            ];
                        }
                        $categorias_compras[$categoria_id]['total'] += $detalle->cantidad;
                    }
                }
            }
        }

        // Determinar la categoría más comprada
        $categoria_preferida = null;
        $max_compras = 0;

        foreach ($categorias_compras as $categoria) {
            if ($categoria['total'] > $max_compras) {
                $max_compras = $categoria['total'];
                $categoria_preferida = $categoria['nombre'];
            }
        }

        return [
            'cliente' => $clientes,
            'categoria_preferida' => $categoria_preferida
        ];
    }

    public function obtenerMetodosPagoCliente($clientes) {
        // Obtener el cliente específico con sus ventas
        //$cliente = $this->find_first((int) $cliente_id);

        // Obtener todas las ventas del cliente
        $ventas = $clientes->getVentas();

        // Array para contar el uso de cada método de pago
        $metodos_pago_usados = [];

        foreach ($ventas as $venta) {
            $metodo_pago = $venta->getMetodoP(); // Relación con metodos_pago
            if ($metodo_pago) {
                $metodo_nombre = $metodo_pago->nombre;
                if (!isset($metodos_pago_usados[$metodo_nombre])) {
                    $metodos_pago_usados[$metodo_nombre] = 0;
                }
                $metodos_pago_usados[$metodo_nombre]++;
            }
        }

        return [
            'cliente' => $clientes,
            'metodos_pago' => $metodos_pago_usados
        ];
    }

    public function obtenerGastoMensual($clientes) {
        // Obtener el cliente específico con sus ventas
        //$cliente = $this->find_first((int) $cliente_id);

        // Inicializar array con 12 meses en 0
        $gasto_mensual = array_fill(1, 12, 0);

        // Obtener todas las ventas del cliente en el año seleccionado
        $ventas = $clientes->getVentas("YEAR(fecha) = 2024");

        foreach ($ventas as $venta) {
            // Obtener mes de la venta
            $mes = (int) date('m', strtotime($venta->fecha));

            // Calcular subtotal de la venta sumando detalles de venta
            $subtotal = 0;
            $detalles_ventas = $venta->getDetalleventa();
            foreach ($detalles_ventas as $detalle) {
                $subtotal += $detalle->importe;
            }

            // Sumar al mes correspondiente
            $gasto_mensual[$mes] += $subtotal;
        }

        return [
            'cliente' => $cliente,
            'gasto_mensual' => $gasto_mensual
        ];
    }
}
