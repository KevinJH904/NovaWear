<?php
class empleados extends ActiveRecord{
    public function initialize() {
        /*     * Nombre de la relacion
         * Nombre de la clase del modelo con quien se relaciona     * Nombre de la columna de relacion
         * Para llamar a la relación
         * -> getVentas
         */
        $this->has_many('ventas', 'model: Ventas', 'fk: empleados_id');

        //Validaciones
        $this->validates_presence_of("nombre"); // El campo no puede ser nulo
        $this->validates_presence_of("email"); // El campo no puede ser nulo
        $this->validates_presence_of("telefono"); // El campo no puede ser nulo

        //Rangos
        $this->validates_length_of('nombre', '40', '2');
        $this->validates_length_of('telefono', '10', '10');

        //email
        $this->validates_email_in("email");

        $this->validates_numericality_of("telefono");



    }

    public function obtenerEstadisticasEmpleados() {
        // Obtener todos los empleados
        $empleados = $this->find();

        // Contadores
        $cantidad_empleados = count($empleados);
        $ventas_por_empleado = [];
        $empleado_mas_ventas = null;
        $max_ventas = 0;
        $total_ventas = 0;

        foreach ($empleados as $empleado) {
            // Inicializar el contador de ventas por empleado
            $ventas_por_empleado[$empleado->id] = 0;

            // Buscar ventas de este empleado
            $ventas = Load::model('ventas')->find("empleados_id = $empleado->id");

            foreach ($ventas as $venta) {
                $ventas_por_empleado[$empleado->id]++;
            }

            // Sumar al total de ventas
            $total_ventas += $ventas_por_empleado[$empleado->id];

            // Verificar si es el empleado con más ventas
            if ($ventas_por_empleado[$empleado->id] > $max_ventas) {
                $max_ventas = $ventas_por_empleado[$empleado->id];
                $empleado_mas_ventas = $empleado->nombre;
            }
        }

        // Calcular promedio de ventas por empleado
        $promedio_ventas = $cantidad_empleados > 0 ? round($total_ventas / $cantidad_empleados, 2) : 0;

        return [
            'cantidad_empleados' => $cantidad_empleados,
            'empleado_mas_ventas' => $empleado_mas_ventas,
            'ventas_por_empleado' => $ventas_por_empleado,
            'promedio_ventas' => $promedio_ventas,
            'empleadosD' => $empleados
        ];
    }

    public function obtenerVentasYGanancias($detalles) {
        // Obtener todos los empleados
        $empleados = $this->find();

        // Arrays para almacenar datos
        $ventas_por_empleado = [];
        $ganancias_por_empleado = [];

        foreach ($empleados as $empleado) {
            $ventas_por_empleado[$empleado->id] = 0;
            $ganancias_por_empleado[$empleado->id] = 0;

            // Obtener las ventas realizadas por el empleado
            $ventas = Load::model('ventas')->find("empleados_id = $empleado->id");

            foreach ($ventas as $venta) {
                $ventas_por_empleado[$empleado->id]++;

                // Obtener detalles de venta y calcular ganancias
                //$detalles = Load::model('detalles_ventas')->find("ventas_id = $venta->id");
                foreach ($detalles as $detalle) {
                    $ganancias_por_empleado[$empleado->id] += $detalle->importe;
                }
            }
        }

        return [
            'empleados' => $empleados,
            'ventas_por_empleado' => $ventas_por_empleado,
            'ganancias_por_empleado' => $ganancias_por_empleado
        ];
    }
}
