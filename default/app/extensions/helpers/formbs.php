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

    public static function btn_limpiar($text = "Limpiar",$attrs = []){
        $text = "🧹 ".$text;
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "btn btn-warning"]);
        return Form::reset($text, $attrs);
    }

    public static function txt($text = "Aceptar", $attrs = []){

        $attrs = Formbs::attrsdefaut($attrs, ["class" => "form-control", "title" => "Nombre del producto"]);
        return Form::text($text, $attrs);
    }

    public static function numero($text = "Aceptar", $attrs = []){
        $attrs = Formbs::attrsdefaut($attrs, ["class" => "form-control", "title" => "Ingrese número"]);
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

    public static function fecha($text = "Aceptar", $attrs = []) {
        // Añadir atributos para formato YYYY-MM-DD
        $attrs = array_merge([
            "class"       => "form-control date",
            "title"       => "Ingrese Fecha (YYYY-MM-DD)",
            "pattern"     => "\d{4}-\d{2}-\d{2}", // Validación básica
            "placeholder" => "YYYY-MM-DD",
            "required"    => true, // Opcional, si es obligatorio
        ], $attrs);

        return Form::date($text, $attrs); // Genera un input type="date"
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
        $lista_Elementos=[$tit];
        foreach ($tabla as $t) {
            array_push($lista_Elementos, $t->nombre);
        }
        $clase = 'select-' . preg_replace('/[^a-zA-Z0-9]/', '-', strtolower($tit));

        $attrs = Formbs::attrsdefaut($attrs, ["class" => "form-control $clase", "title" => "Ingrese Categoría"]);
        // Sanitiza $tit para usarlo como clase CSS
        $script=<<<JS
            <script> new SlimSelect({ select: '.$clase'});</script> 
        JS;

        return Form::select($text, $lista_Elementos,$attrs) . $script;

    }

    public static function seleccion2($text = "Aceptar", $tit="Titulo", $tabla="hola",$attrs = []){
    $lista_Elementos=[$tit];
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
            $text = "↩️ Regresar a " . $text;
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

    public static function input_number($text = "Cancelar", $attrs = []){

    }
    public static function input_email($text = "Cancelar", $attrs = []){}





}