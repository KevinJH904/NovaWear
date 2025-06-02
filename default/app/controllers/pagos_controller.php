<?php

class PagosController extends AppController{
    // listar los pagos realizados
    public function index(){
        $this->pagos= (new Pagos())->find();
        $this->subtitle= "";

    }

    /*
     * /pagos/nuevo //lista a los clientes
     * /pagos/nuevo/:cliente_id //Mostrar las ventas adeudas
     */

    public function nuevo($cliente_id = null){
        $this->subtitle = "PAGOS";
        $this->cliente=null;
        $this->ventas=null;
        $this->metodos= (new metodospago())->find();
        $this->AllClientes = (new Clientes())->find();
//        $this->ClientesConPagosPendientes = (new Clientes())->find_by_sql(
//            "SELECT DISTINCT c.nombre
//                        FROM clientes c
//                        JOIN ventas v ON c.id = v.clientes_id
//                        WHERE v.estado = 'finalizada'"
//        );
        $this->ClientesConPagosPendientes = (new Clientes())->find("id IN (SELECT clientes_id FROM ventas WHERE por_pagar > 0 AND estado = 'finalizada')");



//        $this->AllClientes2 = $this->AllClientes = (new Clientes())->find_all_by_sql(
//                        "SELECT clientes.id, ventas.id as venta_id, ventas.fecha
//                                 FROM clientes
//                                 INNER JOIN ventas ON clientes.id = ventas.cliente_id
//                                 WHERE ventas.estado = 'finalizada'"
//                                    );

        if(Input::hasGet("cliente")) {
            $cliente_id = Input::get("cliente");
            $this->cliente = (new Clientes())->find($cliente_id);

            //inserción del SQL
            //$sql = "INSERT INTO ventas (clientes_id, empleados_id, metodos_pago_id, fecha, total, por_pagar, comentario, forma_pago, cancelada, estado) VALUES ($cliente_id, 1, 1, '2025-04-09', 0, 0, '', '', 0, 'carrito')";
            //$sqlPago = "INSERT INTO pagos (cliente_id, metodo_pago_id, total, comentario) VALUES ($cliente_id,)";
            //(new pagos()) ->sql($sqlPago);

            //$sql="INSERT INTO pagositems (pago_id, venta_id, antes, monto_pagado, adeudo) VALUES (1, 1, 100, 10, 90)";

            //(new Ventas())->sql($sql);
            //(new pagositems())->sql($sql);

            Redirect::toAction("nuevo/{$cliente_id}");
        }

        if($cliente_id != null){
            $this->cliente = (new Clientes())->find($cliente_id);
        }

        if($this->cliente!==null) {
            $this->ventas = (new Ventas())->por_pagar($cliente_id);
        }
    }

    /*
     * params get ?venta[4]=60000 & venta[12]=&venta[19]=5000
     */
    public function finalizar($cliente_id = null)
    {
        $this->subtitle = "Totales a Abonar";
        $this->cliente = (new Clientes())->find($cliente_id);
        $credito_disponible = $this->cliente->credito;
        $ventas = Input::get("ventas");
        $metodo = Input::get("Metodo");
        //venta[4]=100
        $this->ventas_a_pagar = [];
        $this->total_a_abonar = 0;
        foreach ($ventas as $k => $v) {
            if ($v !== "") {
                $item = (new Ventas())->find($k);
                $item->a_abonar = $v;
                $this->total_a_abonar += $v;
                $item->por_pagar = $item->por_pagar - $v; // 👈 aquí actualiza para la vista
                // Obtener comentario específico de la venta
                $comentariosInput = Input::get('comentarios');
                $item->comentario = isset($comentariosInput[$k]) ? $comentariosInput[$k] : "Ninguno";

                $this->ventas_a_pagar[] = $item;
            }
        }

        $ventasPendientes = (new Ventas())->find("estado = 'finalizada' AND por_pagar > 0 AND clientes_id = $cliente_id");


        //sql="INSERT INTO pagositems (pago_id, venta_id, antes, monto_pagado, adeudo) VALUES (1, 1, 100, 10, 90)";

        //(new Ventas())->sql($sql);
        //(new pagositems())->sql($sql);

        $ventasInput = Input::get('ventas'); // ventas[id_venta] => monto
        $comentarioGeneral = Input::get('comentarioGeneral');
        $this->ComentarioGImprimir = "";
        $primer_venta_id = array_key_first($ventasInput); // obtiene el primer id_venta del array
        $venta_origen = (new Ventas())->find_first((int)$primer_venta_id);

        $metodo_pago_id = $venta_origen ? $venta_origen->metodos_pago_id : 8; // usa 1 como fallback si no encuentra

        $total_pagado = 0;
        foreach ($ventasInput as $monto) {
            if (is_numeric($monto) && $monto > 0) {
                $total_pagado += $monto;
            }
        }

//        if ($total_pagado <= 0) {
//            Flash::error("Debe ingresar al menos un monto mayor a cero.");
//        }

//        if ($total_pagado > $credito_disponible) {
//            Flash::error("El cliente no tiene crédito suficiente para abonar $total_pagado. Crédito disponible: $credito_disponible");
//            Redirect::to("/pagos/nuevo/{$cliente_id}"); // O usa return false; según el flujo de tu framework
//        }




        // Creamos el registro en la tabla PAGOS
        $pago = new Pagos();
        $pago->ventass_id = $primer_venta_id;
        $pago->metodo_pago_id = $metodo;
        $pago->total = $total_pagado;
        $pago->comentario = $comentarioGeneral;






        if (!$pago->save()) {
            Flash::error("Error al guardar el pago principal.");
        }
        else{
            $nueva = (new Ventas())->find_first($primer_venta_id);
            $cliente = $nueva->getCliente(); // según su relación belongs_to('cliente', ...)

            if ($cliente) {
                $cliente->aumentar_credito($total_pagado);
            }

            $pagoGuardado = (new Pagos())->find_first($pago->id);
            $this->ComentarioGImprimir = $pagoGuardado->comentario ?? "Sin comentario";


        }

        $comentariosInput = Input::get('comentarios');

        // Recorremos ventas para insertar los pagos por venta
        foreach ($ventasInput as $venta_id => $monto_pagado) {
            if (is_numeric($monto_pagado) && $monto_pagado > 0) {


                $venta = (new Ventas())->find_first((int)$venta_id);
                if ($venta) {
                    $antes = $venta->por_pagar;

                    // Ajuste si el monto pagado excede lo que debe
                    if ($monto_pagado > $antes) {
                        $monto_pagado = $antes; // Se limita a lo que realmente debe
                    }


                    $nuevo_adeudo = max(0, $antes - $monto_pagado);

                    $comentario = isset($comentariosInput[$venta_id]) && $comentariosInput[$venta_id] != ""
                        ? $comentariosInput[$venta_id]
                        : "Ninguno";

                    // Insertamos en pagositems
                    $item = new Pagositems();
                    $item->pago_id = $pago->id;
                    $item->venta_id = $venta_id;
                    $item->antes = $antes;
                    $item->monto_pagado = $monto_pagado;
                    $item->adeudo = $nuevo_adeudo;
                    $item->comentario = $comentario;
                    $item->created_at = date('Y-m-d H:i:s');
                    $item->save();

                    // Actualizamos el saldo pendiente en la venta
                    $venta->por_pagar = $nuevo_adeudo;
                    $venta->save();

                }
            }
        }

        Flash::valid("Pago registrado exitosamente.");

        $this->pagosPP=(new Pagos())->find($cliente_id);

        $clienteAdeudo = (new Clientes())->find($cliente_id);
        //$clienteAdeudo->update_adeudo($cliente_id, $total_pagado);
        $clienteAdeudo->update_credito();

    }

}