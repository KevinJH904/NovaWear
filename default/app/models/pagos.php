<?php
class pagos extends ActiveRecord
{
    public function initialize()
    {

        /*     * Nombre de la relacion
         * Nombre de la clase del modelo con quien se relaciona     * Nombre de la columna de relacion
         * Para llamar a la relación
         * -> getCliente()
         * -> getMetodo()
         */

        $this->belongs_to('Vc', 'model: Ventas', 'fk: ventass_id');
        $this->belongs_to('Vm', 'model: metodospago', 'fk: metodo_pago_id');

        $this->has_many('items', 'model: pagositems', 'fk: pago_id');
    }
}
