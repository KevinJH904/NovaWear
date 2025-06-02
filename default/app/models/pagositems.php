<?php
class pagositems extends ActiveRecord
{
    public function initialize()
    {
        /*     * Nombre de la relacion
         * Nombre de la clase del modelo con quien se relaciona     * Nombre de la columna de relacion
         * Para llamar a la relación
         * -> getPago()
         * -> getventa()
         */

        $this->belongs_to('pago', 'model: Pagos', 'fk: pago_id');
        $this->belongs_to('venta', 'model: Ventas', 'fk: venta_id');
    }
}