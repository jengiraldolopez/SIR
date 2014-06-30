<?php

/**
 * Description of Departamento
 * Las instancias de esta clase se conectan con la base de datos a traves de
 * una referencia que se tiene de PDO.
 * @author tatan313
 */
class Programacion implements Persistible {

    /**
     * Inserta una nueva fila enviada por $_POST
     * @param <type> $argumentos El array que contiene los argumentos enviados por $_POST
     */
    function add($argumentos) {
        extract($argumentos);
        // Observe que se están usando procedimientos almacenados, se hubiera podido usar directamente un INSERT ...
        //insert into programacion_monitores(fk_usuario_monitor,hora_inicio,hora_fin,dia) values ('1700911875','12:00' , '14:00' , 0)
        $ok = UtilConexion::$pdo->exec("insert into programacion_monitores(fk_usuario_monitor,hora_inicio,hora_fin,dia) values ('$fk_usuario_monitor', '$hora_inicio','$hora_fin','$dia')");
//        error_log("lllllllllllllllllllllllllllllllllllllllllllllllll");
//        error_log($ok);
        echo UtilConexion::getEstado();
//        echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "No se pudo agregar el equipo"));
    }

    /**
     * Actualiza una fila.
     * @param <type> $argumentos Un array con el id a buscar y el nuevo tema
     */
    function edit($argumentos) {
        extract($argumentos);
    error_log("se mete putooooooooooooooooooooooooooo");
        
        $ok = UtilConexion::$pdo->exec("update programacion_monitores  set hora_inicio='$hora_inicio',hora_fin='$hora_fin',dia='$dia' 
                                        where id='$id'");
        echo UtilConexion::getEstado();
//        echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "Falló la actualización de los datos"));
    }

    /**
     * Elimina las filas cuyos IDs se pasen como argumentos.
     * @param <type> $argumentos los IDs de los departamentos a ser eliminados.
     * $argumentos es un cadena que contiene uno o varios números separados por
     * comas, que corresponden a los IDs de las filas a eliminar.
     */
    function del($argumentos) {
        extract($argumentos);
        error_log("mesaje de error de eliminacion q es lo q pasa mierda");
        error_log("delete from programacion_monitores where id=$id");
        
        $ok = UtilConexion::$pdo->exec("delete from programacion_monitores where id=$id");
        error_log($ok);
        echo UtilConexion::getEstado();
//        echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "Falló la eliminación"));
    }

    /**
     * Devuelve los datos necesarios para construir una tabla dinámica.
     * @param <type> $argumentos los argumentos enviados por:
     *               Departamento.js.crearTablaDepartamento()
     */
    function select($argumentos) {
        
        $count = UtilConexion::$pdo->query("SELECT id from programacion_monitores")->rowCount();
        // Calcula el total de páginas por consulta
        error_log($count);
        if ($count > 0) {
            $total_pages = ceil($count / $rows);
        } else {
            $total_pages = 0;
        }

        // Si por alguna razón página solicitada es mayor que total de páginas
        // Establecer a página solicitada total paginas  (¿por qué no al contrario?)
        if ($page > $total_pages) {
            $page = $total_pages;
        }

        // Calcular la posición de la fila inicial
        $start = $rows * $page - $rows;
        //  Si por alguna razón la posición inicial es negativo ponerlo a cero
        // Caso típico es que el usuario escriba cero para la página solicitada
        if ($start < 0) {
            $start = 0;
        }

        $respuesta = [
            'total' => $total_pages,
            'page' => $page,
            'records' => $count
        ];

        $sql = "SELECT id,CONCAT(u.nombre,' ',u.apellido) nombre,hora_inicio,hora_fin,CASE WHEN dia=1 THEN 'Lunes' WHEN dia=2 THEN 'Martes' WHEN dia=3 THEN 'Miercoles' WHEN dia=4 THEN 'Jueves' WHEN dia=5 THEN 'Viernes' WHEN dia=6 THEN 'Sabado' WHEN dia=0 THEN 'Domingo' ELSE 'other' END dia from programacion_monitores p,usuario u where p.fk_usuario_monitor=u.codigo ORDER BY fk_usuario_monitor";
        error_log($sql);
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['id'],
                'cell' => [ $fila['nombre'],$fila['dia'],$fila['hora_inicio'],$fila['hora_fin']]   
            ];
        }
     
         error_log("jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj");
        error_log($respuesta);
      
        echo json_encode($respuesta);
    }
    /**
     * Devuelve un array asociativo de la forma: {"id1":"Dato1", "id2":"Dato2", ...,"idN":"DatoN"}
     */
    public function getLista() {
        $filas['0'] = 'Seleccione un diano';
        $filas += UtilConexion::$pdo->query("SELECT cod_pc , fk_sala , observaciones FROM equipo_sala ORDER BY cod_pc")->fetchAll(PDO::FETCH_KEY_PAIR);
        error_log(print_r($filas, true));
        echo json_encode($filas);
    }
      public function getListah() {
        $filas['0'] = 'Seleccione un dia';
        $filas += UtilConexion::$pdo->query("SELECT distinct(dia) FROM programacion_monitores ORDER BY dia")->fetchAll(PDO::FETCH_KEY_PAIR);
        error_log(print_r($filas));
        echo json_encode($filas);
    }
}

?>

