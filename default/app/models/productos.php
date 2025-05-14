<?php
class Productos extends ActiveRecord
{
    public function initialize()
    {
        /*     * Nombre de la relacion
         * Nombre de la clase del modelo con quien se relaciona     * Nombre de la columna de relacion
         * Para llamar a la relación
         * -> getcategoria()
         * -> getdetalleproducto()
         */
        $this->belongs_to('categoria', 'model: Categorias', 'fk: categorias_id');
        $this->has_many('detalleproducto', 'model: detalles_ventas', 'fk: productos_id');

        //Validación de que exista el campo
//        $this->validates_presence_of("nombre"); // El campo no puede ser nulo
//        $this->validates_presence_of("precio"); // El campo no puede ser nulo
//        $this->validates_presence_of("stock"); // El campo no puede ser nulo
//        $this->validates_presence_of("categorias_id"); // El campo no puede ser nulo


        //Validación que este entre 3 a 25 caracteres
        $this->validates_length_of('nombre', '25', '3', 'Error: El nombre debe tener entre 4 y 24 caracteres.');

        //Validación de números Precio y Stock
        $this->validates_numericality_of("precio");
        $this->validates_numericality_of("stock");
        $this->validates_numericality_of("categorias_id");




    }

    public function bajastock($id, $cantidad){
        $sql="UPDATE productos SET stock = GREATEST(0, stock - $cantidad) WHERE id = $id";
        (new Productos())->sql($sql);

    }

    public function obtenerEst($ventas)
    {
        //$ventas = Load::model('detalles_ventas')->find("productos_id=$producto_id");
        $total_unidades_vendidas = 0;
        $total_ingresos = 0;
        $ultima_fecha_venta = 0;
        foreach ($ventas as $v) {
            $total_unidades_vendidas += $v->cantidad;
            $total_ingresos += $v->importe;

            //Obtener la fecha de la venta asociada
            $venta_info = Load::model('ventas')->find_first($v->ventas_id);
            if ($venta_info && (!$ultima_fecha_venta || strtotime($venta_info->fecha) > strtotime($ultima_fecha_venta))) {
                $ultima_fecha_venta = $venta_info->fecha;
            }

        }
        return ['total_unidades_vendidas' => $total_unidades_vendidas,
            'total_ingresos' => $total_ingresos,
            'ultima_fecha_venta' => $ultima_fecha_venta
        ];
    }

    public function TotalVent($ventas)
    {
        //$ventas = Load::model('detalles_ventas')->find("productos_id=$producto_id");
        $total_ventas = 0;
        foreach ($ventas as $v) {
            $total_ventas += $v->cantidad;
        }
        return $total_ventas;
    }

    //Modelo para el total de ventas por mes
    public function TotalMes($ventas)
    {
        // Inicializar un array con 12 meses en 0
        $ventas_por_mes = [];
        for ($i = 1; $i <= 12; $i++) {
            $ventas_por_mes[$i] = [
                'total_unidades_vendidas' => 0,
                'total_ingresos' => 0,
                'ultima_fecha_venta' => null
            ];
        }

        // Recorrer las ventas y distribuirlas por mes
        foreach ($ventas as $v) {
            // Obtener la fecha de la venta asociada
            $venta_info = Load::model('ventas')->find_first($v->ventas_id);

            if ($venta_info) {
                // Obtener el mes de la venta (de 1 a 12)
                $mes = date('n', strtotime($venta_info->fecha));

                // Sumar valores al mes correspondiente
                $ventas_por_mes[$mes]['total_unidades_vendidas'] += $v->cantidad;
                $ventas_por_mes[$mes]['total_ingresos'] += $v->importe;

                // Actualizar la última fecha de venta si es más reciente
                if (!$ventas_por_mes[$mes]['ultima_fecha_venta'] || strtotime($venta_info->fecha) > strtotime($ventas_por_mes[$mes]['ultima_fecha_venta'])) {
                    $ventas_por_mes[$mes]['ultima_fecha_venta'] = $venta_info->fecha;
                }
            }
        }

        return $ventas_por_mes;

    }
}
