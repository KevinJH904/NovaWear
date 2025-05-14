<?php
class detalles_ventas extends ActiveRecord{

    public function initialize() {
        /*     * Nombre de la relacion
         * Nombre de la clase del modelo con quien se relaciona     * Nombre de la columna de relacion
         * Para llamar a la relación
         * -> getVenta()
         * -> getProducto()
         */
        $this->belongs_to('venta', 'model: Ventas', 'fk: ventas_id');
        $this->belongs_to('producto', 'model: Productos', 'fk: productos_id');

        //Validaciones de los campos
        $this->validates_presence_of("ventas_id"); // El campo no puede ser nulo
        $this->validates_presence_of("productos_id"); // El campo no puede ser nulo
        //$this->validates_presence_of("descripcion"); // El campo no puede ser nulo
        //$this->validates_presence_of("cantidad"); // El campo no puede ser nulo
        //$this->validates_presence_of("unitario"); // El campo no puede ser nulo
        //$this->validates_presence_of("subtotal"); // El campo no puede ser nulo
        //$this->validates_presence_of("descuento"); // El campo no puede ser nulo
        ///$this->validates_presence_of("importe"); // El campo no puede ser nulo

        $this->validates_length_of('unitario', '5');
        $this->validates_length_of('descuento', '2');
        $this->validates_length_of('importe', '4');
        //$this->validates_length_of('descripcion', '50', '5');
            

    }

    public function after_save(){
        $this->getVenta()->set_total();
    }

    public function before_save(){
        $producto = (new Productos())->find($this->productos_id);
        $this->importe = $this->unitario * $this->cantidad;
        $this->descripcion = $producto->nombre;
    }
}
