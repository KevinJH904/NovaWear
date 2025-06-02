<?php
class metodospago extends ActiveRecord{
    public function initialize() {
        /*     * Nombre de la relacion
         * Nombre de la clase del modelo con quien se relaciona     * Nombre de la columna de relacion
         * Para llamar a la relación
         * -> getVentas
         */
        $this->has_many('ventas', 'model: Ventas', 'fk: metodos_pago_id');

        $this->has_many('pagosM', 'model: pagos', 'fk: metodo_pago_id');

        //Validaciones
        $this->validates_presence_of("nombre"); // El campo no puede ser nulo
        $this->validates_length_of('nombre', '25', '5');


    }

    public function obtenerVentasPorMetodo($metodos_pago) {

        // Obtener el número total de ventas sin importar el método de pago
        $total_ventas = Load::model('ventas')->count();

        // Obtener el número de ventas realizadas con este método de pago específico
        $ventas_especifico = Load::model('ventas')->count("metodos_pago_id = $metodos_pago->id");

        return [
            'ventas_especifico' => $ventas_especifico,
            'total_ventas' => $total_ventas
        ];
    }

    public function obtenerIngresosPorMetodo($metodo) {
        // Verificar si el método de pago existe
        if (!$metodo) {
            return null; // Si el método de pago no existe, retornamos null
        }

        // Obtener todas las ventas realizadas con este método de pago específico
        $ventas_especificas = Load::model('ventas')->find("metodos_pago_id = $metodo->id");

        // Calcular ingresos totales de este método de pago
        $ingresos_metodo = 0;
        foreach ($ventas_especificas as $venta) {
            $detalles_ventas = $venta->getDetalleventa();
            foreach ($detalles_ventas as $detalle) {
                $ingresos_metodo += $detalle->importe;
            }
        }

        // Obtener todas las ventas de la tienda (sin importar el método de pago)
        $todas_las_ventas = Load::model('ventas')->find();

        // Calcular ingresos totales de todas las ventas
        $ingresos_totales = 0;
        foreach ($todas_las_ventas as $venta) {
            $detalles_ventas = $venta->getDetalleventa();
            foreach ($detalles_ventas as $detalle) {
                $ingresos_totales += $detalle->importe;
            }
        }

        return [
            'ingresos_metodo' => $ingresos_metodo,
            'ingresos_totales' => $ingresos_totales
        ];
    }

    public function obtenerVentasPorMetodoEspecifico($metodo) {
        // Obtener el método de pago específico
        //$metodo = $this->find_first((int) $metodo_id);
        if (!$metodo) {
            return null; // Si el método de pago no existe, retornamos null
        }

        // Obtener todas las ventas realizadas con este método de pago
        $ventas = Load::model('ventas')->find("metodos_pago_id = $metodo->id");

        // Lista de ventas
        $ventas_detalladas = [];

        foreach ($ventas as $venta) {
            // Obtener información del cliente
            $cliente = $venta->getCliente(); // Relación con Cliente
            $cliente_nombre = $cliente ? $cliente->nombre : "No registrado";

            // Obtener el método de pago
            $metodo_pago = $venta->getMetodoP();
            $metodo_pago_nombre = $metodo_pago ? $metodo_pago->nombre : "No especificado";

            // Calcular el total de la venta sumando detalles de venta
            $total_ingreso = 0;
            $detalles_ventas = $venta->getDetalleventa();
            foreach ($detalles_ventas as $detalle) {
                $total_ingreso += $detalle->importe;
            }

            // Guardar la información de la venta
            $ventas_detalladas[] = [
                'venta_id' => $venta->id,
                'fecha_venta' => $venta->fecha,
                'cliente_nombre' => $cliente_nombre,
                'metodo_pago' => $metodo_pago_nombre,
                'total_ingreso' => $total_ingreso
            ];
        }

        return [
            'metodo' => $metodo,
            'ventas_detalladas' => $ventas_detalladas
        ];
    }

    public function TotalMes(){
        return 'hola';
    }

}
