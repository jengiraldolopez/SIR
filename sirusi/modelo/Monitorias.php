<?php

/**
 * Description of Departamento
 * Las instancias de esta clase se conectan con la base de datos a traves de
 * una referencia que se tiene de PDO.
 * @author tatan313
 */
class Monitorias implements Persistible {

    /**
     * Inserta una nueva fila enviada por $_POST
     * @param <type> $argumentos El array que contiene los argumentos enviados por $_POST
     */
    function add($argumentos) {
        extract($argumentos);
        // Observe que se están usando procedimientos almacenados, se hubiera podido usar directamente un INSERT ...
        
        $ok = UtilConexion::$pdo->exec("insert into reserva_equipo(fk_usuario,fk_equipo,fecha_inicio,fecha_fin,observaciones) values ('$fk_usuario',$fk_equipo,'$inicia','$finaliza','$observaciones')");
        
        echo UtilConexion::getEstado();
//        echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "No se pudo agregar el equipo"));
    }

    /**
     * Actualiza una fila.
     * @param <type> $argumentos Un array con el id a buscar y el nuevo tema
     */
    function edit($argumentos) {
        extract($argumentos);
        error_log("XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");
       // error_log("update reserva_equipo set observaciones=$observaciones where cod_pc='$cod_pc'");
        $ok = UtilConexion::$pdo->exec("update reserva_equipo set fk_usuario= '$fk_usuario', fk_equipo=$fk_equipo ,fecha_fin='$finaliza', observaciones='$observaciones' 
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
        $datos = "'{" . $argumentos['id'] . "}'";
        $ok = UtilConexion::$pdo->exec("select tblequipos_eliminar($datos)");
        echo UtilConexion::getEstado();
//        echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "Falló la eliminación"));
    }

    /**
     * Devuelve los datos necesarios para construir una tabla dinámica.
     * @param <type> $argumentos los argumentos enviados por:
     *               Departamento.js.crearTablaDepartamento()
     */
    function select($argumentos) {
        $where = UtilConexion::getWhere($argumentos); // Se construye la clausula WHERE
         extract($argumentos);
        if (isset($foranea)) {
            $where = "WHERE a.fk_sala = '$foranea'
                      and m.fk_cod_estudiante = e.cod_estudiante
                      and m.fk_cod_pc = a.cod_pc";
        } else {
            $where = "WHERE fk_sala = 'ninguno'";
        }
        //error_log("SELECT cod_pc from equipo_sala $where");
        
        //select a.cod_pc , a.observaciones , e.nombres from (select fk_cod_estudiante , fk_cod_pc from reserva_eq_sala
                         //                      
                                      //         
                    //                           
//
//
        error_log("SELECT distinct(cod_pc) from (select fk_cod_estudiante , fk_cod_pc from reserva_eq_sala
                                             where (fk_cod_pc,to_char(fecha_inicio,'HH24:mi')) in (select  fk_cod_pc , max(to_char(fecha_inicio,'HH24:mi')) from reserva_eq_sala 
                                             where to_char(fecha_inicio,'DD-MM-YYYY')='18-12-2013'
                                             group by fk_cod_pc)) m , equipo_sala a , estudiantes e $where");
        
        $count = UtilConexion::$pdo->query("SELECT cod_pc from (select fk_cod_estudiante , fk_cod_pc from reserva_eq_sala
                                             where (fk_cod_pc,to_char(fecha_inicio,'HH24:mi')) in (select  fk_cod_pc , max(to_char(fecha_inicio,'HH24:mi')) from reserva_eq_sala 
                                             where to_char(fecha_inicio,'DD-MM-YYYY')='18-12-2013'
                                             group by fk_cod_pc)) m , equipo_sala a , estudiantes e $where")->rowCount();
        // Calcula el total de páginas por consulta
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

        $sql = "SELECT distinct(cod_pc), observaciones , nombres from (select fk_cod_estudiante , fk_cod_pc from reserva_eq_sala
                                             where (fk_cod_pc,to_char(fecha_inicio,'HH24:mi')) in (select  fk_cod_pc , max(to_char(fecha_inicio,'HH24:mi')) from reserva_eq_sala 
                                             where to_char(fecha_inicio,'DD-MM-YYYY')='$fecha'
                                             group by fk_cod_pc)) m , equipo_sala a , estudiantes e  $where ORDER BY cod_pc $sord LIMIT $rows OFFSET $start";
        //error_log($sql);
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['cod_pc'],
                'cell' => [$fila['cod_pc'], $fila['observaciones'],$fila['nombres']]   
            ];
        }
        // Quite los comentarios para ver el array original y el array codificado en JSON
        // error_log(print_r($respuesta, TRUE));
        // error_log(print_r(json_encode($respuesta), TRUE));
        //error_log($respuesta);
        //error_log("putaaaaaaaaaaaaaaaaaaaaaaa");
        //error_log(print_r($respuesta, true));
        echo json_encode($respuesta);
    }
function selectp($argumentos) {
        $where = UtilConexion::getWhere($argumentos); // Se construye la clausula WHERE
         extract($argumentos);
    
        
        
        $count = UtilConexion::$pdo->query("select id 
                                            from reserva_equipo r , usuario u
                                            where r.fk_usuario = u.codigo 
                                            and to_char(r.fecha_inicio,'DD/MM/YYYY')='$fecha'")->rowCount();
        // Calcula el total de páginas por consulta
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

        $sql = "select id , concat(u.nombre, ' ' ,u.apellido) nombre , fk_equipo ,to_char(r.fecha_inicio,'HH:24') inicia, to_char(r.fecha_fin,'HH:24') finaliza, observaciones 
                                            from reserva_equipo r , usuario u
                                            where r.fk_usuario = u.codigo 
                                            and to_char(r.fecha_inicio,'DD/MM/YYYY')='$fecha' ORDER BY id";
        //error_log($sql);
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['id'],
                'cell' => [$fila['nombre'], $fila['fk_equipo'],$fila['inicia'],$fila['finaliza'],$fila['observaciones']]   
            ];
        }
        // Quite los comentarios para ver el array original y el array codificado en JSON
        // error_log(print_r($respuesta, TRUE));
        // error_log(print_r(json_encode($respuesta), TRUE));
        //error_log($respuesta);
        //error_log("putaaaaaaaaaaaaaaaaaaaaaaa");
        //error_log(print_r($respuesta, true));
        echo json_encode($respuesta);
    }
    /**
     * Devuelve un array asociativo de la forma: {"id1":"Dato1", "id2":"Dato2", ...,"idN":"DatoN"}
     */
    public function getListah() {
        extract($argumentos);
        $filas['0'] = 'Seleccione un equipo';
        $filas += UtilConexion::$pdo->query("SELECT id FROM equipo_sala ORDER BY id")->fetchAll(PDO::FETCH_KEY_PAIR);
        error_log(print_r($filas, true));
        echo json_encode($filas);
    }
    
}

?>

