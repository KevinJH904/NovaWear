<?php

class Formbs{
    protected static function attrsdefaut($attrs, $defaults)
    {
        foreach ($defaults as $k => $v) {
            if (isset($attrs[$k])) {
                if (strpos($attrs[$k], $v) === false) {
                    $attrs[$k] .= ' '.$v;
                }
            } else {
                $attrs[$k] = $v;
            }
        }
        return $attrs;
    }

    // Formbs::btn_aceptar("Aceptar")
    public static function btn_aceptar($text = "Guardar", $attrs = []){
        $text = "💾 ".$text;
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "btn btn-success"]);
        return Form::submit($text, $attrs);
    }

    public static function btn_aceptarVentas($text = "Guardar", $attrs = []){
        $text = "💾 ".$text;
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "btn btn-success"]);

        return Form::submit($text, $attrs);
    }

    public static function btn_limpiar($text = "Limpiar",$attrs = []){
        $text = "🧹 ".$text;
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "btn btn-warning"]);
        return Form::reset($text, $attrs);
    }

    public static function txt($text = "Aceptar", $attrs = [], $ban=0){

        $attrs = Formbs::attrsdefaut($attrs, ["class" => "form-control", "title" => "Nombre del producto"]);

        if ($ban==1) {
            return Form::text($text, $attrs, "Ninguno");
        }
        return Form::text($text, $attrs);
    }

    public static function total($text = "Aceptar", $attrs = [], $ban=null){

        if($ban==0){
            $attrs = Formbs::attrsdefaut($attrs, ["class" => "form-control", "title" => "Nombre del producto", "hidden"=>"true"]);
            return Form::text($text, $attrs,0);
        }
        elseif($ban==1){
            $attrs = Formbs::attrsdefaut($attrs, ["class" => "form-control", "title" => "Nombre del producto", "hidden"=>"true"]);
            return Form::text($text, $attrs,1);
        }

    }

    public static function numero($text = "Aceptar", $attrs = []){
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "form-control", "title" => "Ingrese número"]);
        return Form::number($text, $attrs);
    }

    public static function numeroUS($text = "Aceptar", $attrs = []){
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "form-control", "title" => "Ingrese número", "hidden"=>"true"]);
        return Form::number($text, $attrs);
    }

    public static function passwd($text = "Aceptar", $attrs = []){
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "form-control", "title" => "Ingrese una contraseña"]);
        return Form::password($text, $attrs);
    }

    public static function numeroDouble($text = "Aceptar", $attrs = []){
        $attrs = Formbs::attrsdefaut($attrs, [
            "class" => "form-control",
            "title" => "Ingrese número",
            "step" => "any" // Permite ingresar valores decimales
        ]);
        return Form::number($text, $attrs);
    }



    ## kevin@gmail.com
    public static function correo($text = "Aceptar", $attrs = []){
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "form-control", "title" => "Email"]);
        return Form::email($text, $attrs);
    }

    public static function fecha($text = "Aceptar", $attrs = [], $val) {
        // Añadir atributos para formato YYYY-MM-DD
        $attrs = Formbs::attrsdefaut($attrs, [
            "class"       => "form-control date",
            "title"       => "Ingrese Fecha (YYYY-MM-DD)",
            "pattern"     => "\d{4}-\d{2}-\d{2}", // Validación básica
            "placeholder" => "YYYY-MM-DD",
            "required"    => true, // Opcional, si es obligatorio
            "readonly"    => "readonly", // Solo lectura
        ]);

        return Form::date($text, $attrs, $val); // Genera un input type="date"
    }

    public static function fechaOculta($text= "Aceptar", $attrs = []) {

        $valor = date("Y-m-d");

        // Campo oculto con valor por defecto
        $attrs = array_merge([
            "class"       => "form-control date",
            "title"       => "Ingrese Fecha (YYYY-MM-DD)",
            "pattern"     => "\d{4}-\d{2}-\d{2}", // Validación básica
            "placeholder" => "YYYY-MM-DD",
            "value"       =>  $valor,
        ], $attrs);

        return Form::date($text, $attrs);
    }

    public static function decimal($text = "Aceptar", $attrs = []){
        // Agregar los atributos por defecto y asegurarse de que acepte números decimales con dos decimales
        $attrs = Formbs::attrsdefaut($attrs, [
            "class" => "form-control",
            "title" => "Ingrese número",
            "type" => "number",
            "step" => "0.01",
            "pattern" => "\\d+(\\.\\d{2})?"
        ]);
        return Form::number($text, $attrs);
    }

    public static function seleccion($text = "Aceptar", $tit="Titulo", $tabla="hola",$attrs = []){
        //$lista_Elementos=["-1"=>$tit];
        $lista_Elementos=[];
        foreach ($tabla as $t) {
            $lista_Elementos[$t->id] = $t->nombre; // Asumimos que cada elemento tiene un 'id'
        }
        $clase = 'select-' . preg_replace('/[^a-zA-Z0-9]/', '-', strtolower($tit));

        $attrs = Formbs::attrsdefaut($attrs, [
            "value" => "",
            "class" => "form-control $clase",
            "title" => "Ingrese Categoría",
            "required"    => true, // Opcional, si es obligatorio
        ]);
        // Sanitiza $tit para usarlo como clase CSS
        $script=<<<JS
            <script> new SlimSelect({ select: '.$clase'});</script> 
        JS;

        return Form::select($text, $lista_Elementos,$attrs) . $script;
    }

    public static function seleccionemp($text = "Aceptar", $tit = "Tabla", $tabla = "hola", $attrs = []) {
        $lista_Elementos = [$tit, $tabla[0]->nombre];
        $clase = 'select-' . preg_replace('/[^a-zA-Z0-9]/', '-', strtolower($tit));

        // Agregar atributo readonly y estilo para parecer deshabilitado pero visible
        $attrs = Formbs::attrsdefaut($attrs, [
            "class" => "form-control $clase",
            "title" => "Ingrese Categoría",
            "readonly" => "readonly",
            "style" => "pointer-events: none; background-color: #e9ecef;"
        ]);

        $script = <<<JS
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new SlimSelect({
                    select: '.$clase',
                    disabled: true, // Deshabilita la interacción
                    showContent: 'down',
                    data: [
                        {text: '$tit', value: '$tit'},
                        {text: '{$tabla[0]->nombre}', value: '{$tabla[0]->id}', selected: true}
                    ]
                });
            });
        </script>
    JS;

        return Form::select($text, $lista_Elementos, $attrs, $tabla[0]->nombre) . $script;
    }

    public static function txtEmpleado($name = "venta.empleados_id", $titulo = "Empleado", $empleados = [], $attrs = []) {
        if (!isset($empleados[0])) {
            return '';
        }

        $empleado = $empleados[0];

        // Creamos una lista con una sola opción: ID => Nombre
        $lista = [
            $empleado->id => $empleado->nombre
        ];

        $attrs = Formbs::attrsdefaut($attrs, [
            "class" => "form-control",
            "readonly" => "readonly", // Solo efecto visual, no evita cambios con JS
            "title" => "Empleado seleccionado"
        ]);


        return Form::text($name, $lista, $attrs, $empleado->id);
    }



    public static function seleccion2($text = "Aceptar", $tit="Titulo", $tabla="hola",$attrs = []){
    //$lista_Elementos=[$tit];
    $lista_Elementos=[-1];
    foreach ($tabla as $t) {
        array_push($lista_Elementos, $t->id);
    }
    $clase = 'select-' . preg_replace('/[^a-zA-Z0-9]/', '-', strtolower($tit));

    $attrs = Formbs::attrsdefaut($attrs, ["class" => "form-control $clase", "title" => "Ingrese Categoría"]);
    // Sanitiza $tit para usarlo como clase CSS
    $script=<<<JS
            <script> new SlimSelect({ select: '.$clase'});</script> 
        JS;

    return Form::select($text, $lista_Elementos,$attrs) . $script;

}

    public static function btn_cancelar($text = "Cancelar", $attrs = []){}
    public static function btn_regresar($text = "Cancelar", $attrs = []){}
    public static function link_regresar($text = "Index", $link="index", $img="📝",$attrs = []) {
        if (strpos($link, '/nueva') !== false) {

            $text = $img." Registrar " . $text;
        }

        else{

            if (strpos($link, '/') !== false) {
                $text = $img." Registrar " . $text;
            }
            else
            {
                $text = "↩️ Regresar a " . $text;
            }
        }


        $url = URL_APP . "/" . $link;
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "btn btn-info", "onclick" => "location.href='$url'"]);
        return Form::button($text, $attrs);
    }

    public static function link_abonar($text = "Index", $link="index", $img="📝",$attrs = []) {

        $text = "💰 Abonar más";

        $url = URL_APP . "/pagos/nuevo/". $link;
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "btn btn-info", "onclick" => "location.href='$url'"]);
        return Form::button($text, $attrs);
    }


    public static function link_regresar2($text = "Index", $link="index",$attrs = []) {

        if (strpos($link, '/nueva') !== false) {
            $text = "↩️ Regresar a la selección del " . $text;
        }
        else{
            $text = "💵 Ir a pagar ";
        }



        $url = URL_APP . "/" . $link;
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "btn btn-info", "onclick" => "location.href='$url'"]);
        return Form::button($text, $attrs);
    }




    public static function input_number($text = "Cancelar", $attrs = []){

    }
    public static function input_email($text = "Cancelar", $attrs = []){}





}