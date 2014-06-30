<?php

/**
 * Description of Departamento
 * Las instancias de esta clase se conectan con la base de datos a traves de
 * una referencia que se tiene de PDO.
 * @author tatan313
 */
class Control implements Persistible {

    /**
     * Inserta una nueva fila enviada por $_POST
     * @param <type> $argumentos El array que contiene los argumentos enviados por $_POST
     */
    function add($argumentos) {
        extract($argumentos);
        // Observe que se están usando procedimientos almacenados, se hubiera podido usar directamente un INSERT ...
        
        $ok = UtilConexion::$pdo->exec("insert into tblequipos values('$id', '$nombre','$estado')");
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
        error_log("update equipo_sala set observaciones=$observaciones where cod_pc='$cod_pc'");
        $ok = UtilConexion::$pdo->exec("update equipo_sala set observaciones='$observaciones' 
                                        where cod_pc='$cod_pc'");
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
        //$where = UtilConexion::getWhere($argumentos); // Se construye la clausula WHERE
         //extract($argumentos);
        //if (isset($foranea)) {
          //  $where = "WHERE a.fk_sala = '$foranea'
            //          and m.fk_cod_estudiante = e.cod_estudiante
//                      and m.fk_cod_pc = a.cod_pc";
//        } else {
//            $where = "WHERE fk_sala = 'ninguno'";
//        }
        //error_log("SELECT cod_pc from equipo_sala $where");
        
        //select a.cod_pc , a.observaciones , e.nombres from (select fk_cod_estudiante , fk_cod_pc from reserva_eq_sala
                         //                      
                                      //         
                    //                           
//
//
               
        $count = UtilConexion::$pdo->query("SELECT id from programacion_monitores")->rowCount();
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

        $sql = "SELECT id,monitor,hora_inicio,hora_fin,dia from programacion_monitores ORDER BY id $sord LIMIT $rows OFFSET $start";
        //error_log($sql);
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['id'],
                'cell' => [$fila['id'], $fila['monitor'],$fila['hora_inicio'],$fila['hora_fin'],$fila['dia']]   
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
         // mon:idMonitor, sal:idConsa,desd:desde,hasta:hasta
         
         $sub=substr($sal, 0, -4);
         
         error_log($mon);
         error_log($sub);
         error_log($sal);
         error_log($desd);
        if ($mon<>0) {
            $where .= " and u.codigo = '$mon'";
        } 
        
        
        if ($sub=='s') {
            $where .= " and s.nombre = '$sal'";
        } 
        
        if (!empty($desd)){
            $where .= " and to_date((to_char(c.hora_fin,'DD/MM/YYYY')),'DD/MM/YYYY') > '$desd'";
            
        }
        if (!empty($hasta)){
            $where .= " and to_date((to_char(c.hora_fin,'DD/MM/YYYY')),'DD/MM/YYYY') < '$hasta'";
            
        }
        error_log("select c.id from control_monitorias c,usuario u,sala s,programacion_monitores p
                                            where c.fk_sala=s.nombre
                                            and c.fk_programacion_monitores = p.id
                                            and p.fk_usuario_monitor = u.codigo $where");
        
        $count = UtilConexion::$pdo->query("select c.id from control_monitorias c,usuario u,sala s,programacion_monitores p
                                            where c.fk_sala=s.nombre
                                            and c.fk_programacion_monitores = p.id
                                            and p.fk_usuario_monitor = u.codigo $where")->rowCount();
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

        $sql = "select c.id id,u.nombre nombreu,s.nombre nombres,to_char(c.hora_fin,'DD/MM/YYYY') fecha,to_char(c.hora_inicio,'HH24:mi') horai,to_char(c.hora_fin,'HH24:mi') horaf from control_monitorias c,usuario u,sala s,programacion_monitores p
                where c.fk_sala=s.nombre
                and c.fk_programacion_monitores = p.id
                and p.fk_usuario_monitor = u.codigo 
                $where ORDER BY c.id ";
        //error_log($sql);
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['id'],
                'cell' => [$fila['nombreu'], $fila['nombres'],$fila['fecha'],$fila['horai'],$fila['horaf']]   
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
    public function getLista() {
        $filas['0'] = 'Seleccione un equipo';
        $filas += UtilConexion::$pdo->query("SELECT cod_pc , fk_sala , observaciones FROM equipo_sala ORDER BY cod_pc")->fetchAll(PDO::FETCH_KEY_PAIR);
        error_log(print_r($filas, true));
        echo json_encode($filas);
    }
     public function getSelectj($argumentos) {
        //extract($argumentos);
        extract($argumentos);
        error_log("holaaaaaaaaaaa");
        $select = "<label>Monitor: </label><select style='width: 300px;'id=monitores>";
        $select .= "<option value='0'>Todos los monitores</option>";
        foreach (UtilConexion::$pdo->query("select codigo,nombre,apellido from usuario where fk_rol=1") as $fila) {
            $select .= "<option value='{$fila['codigo']}'> Nombre: {$fila['nombre']} {$fila['apellido']} Cod: {$fila['codigo']}</option>";
        }
        $select .= "</select><label>   Sala :</label><select style='width: 300px;' id=salas>";
        $select .= "<option value='0'>Todas las salas</option>";
        
         foreach (UtilConexion::$pdo->query("select nombre,fk_bloque from sala") as $fila) {
            $select .= "<option value='{$fila['nombre']}'>  {$fila['nombre']} - {$fila['fk_bloque']}</option>";
        }
        
        echo json_encode($select . "</select>");
    }
}

?>

