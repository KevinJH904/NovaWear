<?php
class Categorias extends ActiveRecord{
    public function initialize() {
        /*     * Nombre de la relacion
         * Nombre de la clase del modelo con quien se relaciona     * Nombre de la columna de relacion
         * Para llamar a la relación
         * -> getProductos
         */
        $this->has_many('productos', 'model: Productos', 'fk: categorias_id');

        //Validaciones de los campos
        $this->validates_presence_of("nombre"); // El campo no puede ser nulo

        $this->validates_length_of('nombre', '40', '4');


    }

    public function obtenerDatosCategoria($categoria) {
        // Obtener la categoría específica
        //$categoria = $this->find_first((int) $categoria_id);
        if (!$categoria) {
            return null; // Si la categoría no existe, retornamos null
        }

        // Inicializar datos
        $ventas_categoria = 0;
        $ingresos_categoria = 0;
        $stock_categoria = 0;
        $ventas_totales = 0;

        // Obtener todos los productos de la categoría
        $productos = $categoria->getProductos();

        foreach ($productos as $producto) {
            $stock_categoria += $producto->stock;

            // Obtener detalles de ventas de este producto usando la relación has_many
            $detalles_ventas = $producto->detalleproducto();

            foreach ($detalles_ventas as $detalle) {
                $ventas_categoria += $detalle->cantidad;
                $ingresos_categoria += $detalle->importe;
            }
        }

        // Obtener las ventas de TODAS las categorías
        $todas_las_categorias = $this->find();
        foreach ($todas_las_categorias as $cat) {
            if ($cat->id != $categoria) {
                $productos_cat = $cat->getProductos();
                foreach ($productos_cat as $producto) {
                    // Obtener detalles de ventas de este producto usando la relación has_many
                    $detalles = $producto->detalleproducto();
                    foreach ($detalles as $detalle) {
                        $ventas_totales += $detalle->cantidad;
                    }
                }
            }
        }

        return [
            'categoria' => $categoria,
            'ventas_categoria' => $ventas_categoria,
            'ingresos_categoria' => $ingresos_categoria,
            'stock_categoria' => $stock_categoria,
            'ventas_otros' => $ventas_totales
        ];
    }

    public function obtenerComparacionIngresos($categoria) {
        // Obtener la categoría específica
        //$categoria = $this->find_first((int) $categoria_id);
        if (!$categoria) {
            return null; // Si la categoría no existe, retornamos null
        }

        // Inicializar ingresos
        $ingresos_categoria = 0;
        $ingresos_totales = 0;

        // Obtener ingresos de la categoría seleccionada
        $productos = $categoria->getProductos();
        foreach ($productos as $producto) {
            $detalles_ventas = $producto->detalleproducto();
            foreach ($detalles_ventas as $detalle) {
                $ingresos_categoria += $detalle->importe;
            }
        }

        // Obtener ingresos de todas las demás categorías
        $todas_las_categorias = $this->find("id != $categoria->id");
        foreach ($todas_las_categorias as $cat) {
            $productos_cat = $cat->getProductos();
            foreach ($productos_cat as $producto) {
                $detalles = $producto->detalleproducto();
                foreach ($detalles as $detalle) {
                    $ingresos_totales += $detalle->importe;
                }
            }
        }

        return [
            'ingresos_categoria' => $ingresos_categoria,
            'ingresos_totales' => $ingresos_totales
        ];
    }


    /*

    public function obtenerEstadisticasCategorias($categorias) {
        // Obtener todas las categorías
        //$categorias = $this->find();


        // Arrays para almacenar datos


        $ventas_por_categoria = [];
        $ingresos_por_categoria = [];
        $stock_por_categoria = [];

        // Variables para encontrar la categoría más vendida
        $categoria_mas_vendida = null;
        $max_ventas = 0;

        foreach ($categorias as $categoria) {
            $ventas_por_categoria[$categoria->id] = 0;
            $ingresos_por_categoria[$categoria->id] = 0;
            $stock_por_categoria[$categoria->id] = 0;

            // Obtener productos de la categoría
            //$productos = Load::model('productos')->find("categorias_id = $categoria->id");

            // Obtener productos de la categoría usando la relación has_many
            $productos = $categoria->getProductos();

            foreach ($productos as $producto) {
                // Sumar stock disponible
                $stock_por_categoria[$categoria->id] += $producto->stock;

                // Buscar ventas de este producto
               // $detalles_ventas = Load::model('detalles_ventas')->find("productos_id = $producto->id");

                foreach ($detalles_ventas as $detalle) {
                    $ventas_por_categoria[$categoria->id] += $detalle->cantidad;
                    $ingresos_por_categoria[$categoria->id] += $detalle->subtotal;
                }
            }

            // Verificar si esta es la categoría más vendida
            if ($ventas_por_categoria[$categoria->id] > $max_ventas) {
                $max_ventas = $ventas_por_categoria[$categoria->id];
                $categoria_mas_vendida = $categoria->nombre;
            }
        }

        return [
            'categorias' => $categorias,
            'ventas_por_categoria' => $ventas_por_categoria,
            'ingresos_por_categoria' => $ingresos_por_categoria,
            'stock_por_categoria' => $stock_por_categoria,
            'categoria_mas_vendida' => $categoria_mas_vendida
        ];


    }

    */

}
