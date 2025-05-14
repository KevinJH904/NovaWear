<?php

class Htmlbs
{
    protected static function attrsdefaut($attrs, $defaults)
    {
        foreach ($defaults as $k => $v) {
            if (isset($attrs[$k])) {
                if (strpos($attrs[$k], $v) === false) {
                    $attrs[$k] .= ' ' . $v;
                }
            } else {
                $attrs[$k] = $v;
            }
        }
        return $attrs;
    }

    public static function img($src, $alt = '', $attrs = []){
        $attrs = Htmlbs::attrsdefaut($attrs, ["class" => ""]);
        return '<img src="'.PUBLIC_PATH."storage/$src\" alt=\"$alt\" ".Tag::getAttrs($attrs).'/>';

    }

    public static function link_app($text, $link, $alt = '', $attrs = []){
        $attrs = Htmlbs::attrsdefaut($attrs, ["class" => ""]);
        return '<a href="'.URL_APP.'/'.$link.'" alt="'.$alt.'"'.Tag::getAttrs($attrs).'/>'.$text.'</a> ';
    }

    public static function link_app2($link, $alt = '', $attrs = []){
        $attrs = Htmlbs::attrsdefaut($attrs, ["class" => "dropdown-item"]);
        return '<a href="'.URL_APP.'/'.$link.'" alt="'.$alt.'"'.Tag::getAttrs($attrs).'/>';
    }
}