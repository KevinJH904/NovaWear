<?php

class EmpleadosController extends AppController{
    public function index() {
        $this -> subtitle = "Lista de Empleados";
        $this->empleados = (new Empleados())->find();
    }
    public function ver($id){
        $this -> subtitle = "Empleado Específico";
        $this->empleados= (new Empleados())->find($id);


        $datos = Load::model('empleados')->obtenerEstadisticasEmpleados();
        $this->cantidad_empleados = $datos['cantidad_empleados'];
        $this->empleado_mas_ventas = $datos['empleado_mas_ventas'];
        $this->ventas_por_empleado = $datos['ventas_por_empleado'];
        $this->promedio_ventas = $datos['promedio_ventas'];
        $this->empleadosD = $datos['empleadosD'];

        ///////////////////////////
        $datos2 = Load::model('empleados')->obtenerVentasYGanancias((new detalles_ventas())->find("ventas_id=$id"));
        $this->empleados2 = $datos2['empleados'];
        $this->ventas_por_empleado2 = $datos2['ventas_por_empleado'];
        $this->ganancias_por_empleado2 = $datos2['ganancias_por_empleado'];

    }

    public function registrar(){

        $this->subtitle = "Registrar Empleados";

        if(Input::hasPost('empleado')){
            $empleado=new Empleados(Input::post('empleado'));


            if($empleado->create()){
                Flash::valid("Empleado registrado");
                Input::delete();
            }else {
                Flash::error("Error al registrar empleado");
            }


        }
    }

    public function subir($modelo,$id){
        View::select(null);
        View::template(null);
        //echo json_encode($_FILES);

        $archivo = $_FILES['fileup'];
        $directorio = "/var/www/html/kumbiaphp/default/public/storage2/$modelo";

        if (!is_dir($directorio)) {
            if (mkdir($directorio, 0755, true)) {
                echo "¡Carpeta creada exitosamente!";
            } else {
                echo "Error: No se pudo crear la carpeta.";
            }
        }

        $ruta_archivo = $directorio . $archivo['name'];
        if (move_uploaded_file($archivo['tmp_name'],$ruta_archivo )) {
            echo json_encode(['success' => true, 'archivo' => $archivo['name']]);
        } else {
            echo json_encode(['error' => 'Error al guardar el archivo']);
        }

        $extension = pathinfo($ruta_archivo, PATHINFO_EXTENSION); // Ejemplo: "png"
        $nueva_ruta = $directorio ."/$id.$extension";
        rename($ruta_archivo, $nueva_ruta);
    }

}