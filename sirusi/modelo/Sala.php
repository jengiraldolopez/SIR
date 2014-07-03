<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sala
 *
 * @author USUARIO
 */
class Sala {

//modificando

    public function getEventosSalas($argumentos) {
        extract($argumentos);
        $eventos = $this->getReservasSalas($idSala) + $this->getRestricciones($idSala);
        echo json_encode($eventos);
    }

    public function getReservasSalas($idSala) {
        $where = '';

        error_log($idSala);
        if ($idSala != 9999) {
            $where = "where rs.fk_sala='$idSala'";
        }
        $sql = "SELECT rs.id,rs.fecha_inicio, rs.fecha_fin, rs.actividad actividad ,u.nombre || ' ' ||u.apellido as usuario, fk_sala sala ,estado, observaciones ,rs.fk_responsable responsable,color
 FROM usuario u inner join reserva_sala rs on u.codigo=rs.fk_usuario $where ";
        $reservas = [];
        foreach (UtilConexion::$pdo->query($sql) as $lista) {
            $reservas[] = [
                'id' => $lista['id'],
                'title' => $lista['usuario'],
                'sala' => $lista['sala'],
                'start' => $lista['fecha_inicio'],
                'end' => $lista['fecha_fin'],
                'actividad' => $lista['actividad'],
                'estado' => $lista['estado'],
                'responsable' => $lista['responsable'],
                'observaciones' => $lista['observaciones'],
                'color' => $lista['color'],
                'infoComplementaria' => 'Reservado pro: ' . $lista['usuario'] . "\nSala: " . $lista['sala'] . "\nresponsable: " . $lista['responsable'],
                'allDay' => FALSE
            ];
        }
        return $reservas;
    }

    private function getRestricciones($idSala) {
        error_log($idSala);
        $where = 'where rc.fk_grupo=g.id and u.codigo=rc.fk_usuario';

        if ($idSala != 9999) {
            $where = "where rc.fk_sala='$idSala' and rc.fk_grupo=g.id
                and rc.fk_usuario = u.codigo";
        }
        $sql = "  select rc.id id,rc.color color,rc.fk_sala sala,rc.fk_grupo grupo ,rc.dia dia ,rc.modalidad modalidad,g.fecha_inicio||' '|| rc.hora_inicio as fecha_inicio,g.fecha_fin||' '|| rc.hora_fin as fecha_fin,u.nombre||' '|| u.apellido usuario
  from restriccion_calendario rc, grupos g, usuario u $where";

        $restricciones = [];
        foreach (UtilConexion::$pdo->query($sql) as $restriccion) {
            $inicio = new DateTime($restriccion['fecha_inicio']);
            $fin = new DateTime($restriccion['fecha_fin']);
            $hora_inicio = $inicio->format('H:i:s');
            $hora_fin = $fin->format('H:i:s');
            $dia = $restriccion['dia'];
            $interval = dateInterval::createFromDateString('1 day');
            $fechas = new DatePeriod($inicio, $interval, $fin);
            $i = 1000;

            foreach ($fechas as $fecha) {
                $fecha = $fecha->format('Y-m-d');
                $inicio = "$fecha $hora_inicio";
                $fin = "$fecha $hora_fin";
                $diaSemana = date("w", strtotime($fecha));

                if ($diaSemana == $dia) {
                    $i++;
                    $restricciones[] = [
                        'id' => "$i-" . $restriccion['id'],
                        'title' => $restriccion['usuario'],
                        'sala' => $restriccion['sala'],
                        'start' => $inicio,
                        'end' => $fin,
                        'grupo' => $restriccion['grupo'],
                        'color' => $restriccion['color'],
                        'dia' => $restriccion['dia'],
                        'modalidad' => $restriccion['modalidad'],
                        'infoComplementaria' => 'restriccion: ' . $restriccion['usuario'] . "\nSala: " . $restriccion['sala'] . "\nresponsable: " . $restriccion['modalidad'],
                        'allDay' => FALSE];
                }
            }
        }
        return $restricciones;
    }

    public function getSelect($argumentos) {
        $id = 'cbo' . rand(0, 9999);
        extract($argumentos);
        $select = "<select id='$id'>";
        $select .= "<option value='0'>Seleccione una sala</option>";
        foreach (UtilConexion::$pdo->query("SELECT s.nombre, 'Sala ' || s.nombre || ' bloque ' || s.fk_bloque AS nombre_sala
                                            FROM sala s
                                            JOIN bloque bl ON bl.nombre = s.fk_bloque
                                            ORDER BY bl.nombre, s.nombre") as $fila) {
            $select .= "<option value='{$fila['nombre']}'>{$fila['nombre_sala']}</option>";
        }
        $select .= "</select>";
        echo tipoRetorno == 'json' ? json_encode($select) : $select;
    }

    public function insertarReservaSala($argumentos) {
        extract($argumentos);
//        error_log("start--------->" . $start);
        $inicio = new DateTime($start);
        $hora_inicio = $inicio->format('H:i:s');
        $fin = new DateTime($end);
        $hora_fin = $fin->format('H:i:s');
        $interval = DateInterval::createFromDateString('1 day');
        $fechas = new DatePeriod($inicio, $interval, $fin);

        UtilConexion::$pdo->beginTransaction();

        $sql = "INSERT INTO reserva_sala(fecha_inicio, fecha_fin, actividad, fk_usuario, fk_sala,estado, observaciones, fk_responsable, color)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?,?) RETURNING id";
        $stmt = UtilConexion::$pdo->prepare($sql);
        $mensaje = '';



//        foreach ($fechas as $fecha) {
//
//            $fecha = $fecha->format('Y-m-d');
//            $inicio = "$fecha $hora_inicio";
//            $fin = "$fecha $hora_fin";
//             $diaSemana = date("w", strtotime($fecha));
//            $ok = true;
//            if ($this->estadisponible($fk_Sala, $diaSemana, $inicio, $fin, $hora_inicio, $hora_fin) === false) {
//                $ok = false;
//                $mensaje = "no se puede realizar porq hay cruce de horarios en :\n$inicio - $fin";
//                echo json_encode(['ok' => $ok, 'mensaje' => $mensaje]);
//                return;
//            }
//        }


        foreach ($fechas as $fecha) {
            $fecha = $fecha->format('Y-m-d');
            $inicio = "$fecha $hora_inicio";
            $fin = "$fecha $hora_fin";
            $diaSemana = date("w", strtotime($fecha));
            $ok = FALSE;
            if (count($dias) == 0) {
                $dias[] = $diaSemana;
            }
            if (in_array($diaSemana, $dias)) {
                  if ($this->estadisponible($fk_Sala, $diaSemana, $inicio, $fin, $hora_inicio, $hora_fin)) {
                    error_log("esta disponible");
                    $ok = $stmt->execute(array($inicio, $fin, $actividad, $fk_usuario, $fk_Sala, $estado, $observaciones, $responsable, $color));
                    if (!$ok) {
                        $mensaje .="$inicio--$fin\n";
                        error_log("no inserta");
                    }else{
                        error_log("ok");
                    }
                } else {
                     $mensaje .="$inicio--$fin\n";
                     error_log("nmensje   ".$mensaje);
                }
            }
        }  //fin de for

        $ok = TRUE;
        if ($mensaje) {
            $mensaje = "fallo la insercion de los siguientes registros:\n$mensaje";
            $ok = FALSE;
        }

        if ($mensaje) {
            if ($obligarEjecucion) {
                UtilConexion::$pdo->commit();
                $ok=TRUE;
                $mensaje='';
            } else {
                UtilConexion::$pdo->rollBack();
            }
        } else{
            UtilConexion::$pdo->commit();
        }

        echo json_encode(['ok' => $ok, 'mensaje' => $mensaje]);
    }

    function getF_inicio($grupo) {
        $sql = "select fecha_inicio from grupos where id='$grupo'";
        foreach (UtilConexion::$pdo->query($sql) as $fecha) {
            $fecha_inicio = $fecha['fecha_inicio'];
        }
        return $fecha_inicio;
    }

    function getF_fin($grupo) {
        $sql = "select fecha_fin from grupos where id='$grupo'";
        foreach (UtilConexion::$pdo->query($sql) as $fecha) {
            $fecha_fin = $fecha['fecha_fin'];
        }
        return $fecha_fin;
    }

    public function insertarRestriccion($argumentos) {
        extract($argumentos);
        $inicio = self::getF_inicio($grupo);
        $fin = self::getF_fin($grupo);
//        $sala=$fk_sala;
        $h_inicio = $start;
        $h_fin = $end;


        $sql = "INSERT INTO restriccion_calendario(hora_inicio, hora_fin, fk_usuario, color, fk_sala, fk_grupo, dia,modalidad)
                VALUES ( ?, ?, ?, ?, ?, ?, ?, ?) RETURNING id";
        $stmt = UtilConexion::$pdo->prepare($sql);
        $mensaje = '';


        $idEvento = FALSE;
        $ok = FALSE;
        if ($this->disponible($sala, $dia, $inicio, $fin, $h_inicio, $h_fin)) {
            error_log("$this->disponible($sala, $dia, $inicio, $fin, $h_inicio, $h_fin)");
            error_log($usuario);
            $ok = $stmt->execute(array($h_inicio, $h_fin, $usuario, $color, $sala, $grupo, $dia, $modalidad));
            if (!$ok) {

                $mensaje .="$inicio--$fin\n";
            }
        } else {
            $mensaje = "No se puede insertar porque ya hay un evento:\n";
        }

//        }
        $ok = TRUE;
        if ($mensaje) {
            $mensaje = "fallo la insercion de los siguientes registros:\n$mensaje";
            $ok = FALSE;
        }

        echo json_encode(['ok' => $ok, 'mensaje' => $mensaje]);
    }

    function disponible($sala, $dia, $fechaInicio, $fechaFin, $horaInicio, $horaFin) {
        $disponible = FALSE;
        error_log("SELECT sala_disponible1('$sala', $dia, '$fechaInicio', '$fechaFin','$horaInicio', '$horaFin')");
        if (($rs = UtilConexion::$pdo->query("SELECT sala_disponible1('$sala', $dia, '$fechaInicio', '$fechaFin','$horaInicio', '$horaFin')"))) {
            if (($fila = $rs->fetch(PDO::FETCH_ASSOC))) {
                error_log(print_r($fila, 1));
                UtilConexion::getEstado();
                if (isset($fila['sala_disponible1'])) {
                    $disponible = $fila['sala_disponible1'] ? TRUE : FALSE;
                }
            }
        }
        error_log($disponible);
        return $disponible;
    }

    function estadisponible($sala, $dia, $fechaInicio, $fechaFin, $horaInicio, $horaFin) {
        $disponible = FALSE;
        error_log("SELECT sala_disponible2('$sala', $dia, '$fechaInicio'::TIMESTAMP, '$fechaFin'::TIMESTAMP,'$horaInicio'::TIME, '$horaFin'::TIME)");
        if ($rs = UtilConexion::$pdo->query("SELECT sala_disponible2('$sala', $dia, '$fechaInicio'::TIMESTAMP, '$fechaFin'::TIMESTAMP,'$horaInicio'::TIME, '$horaFin'::TIME)")) {
            if ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                UtilConexion::getEstado();
                if (isset($fila['sala_disponible2'])) {
                    error_log("datossssssssssssss".$fila['sala_disponible2']);
                    $disponible = $fila['sala_disponible2'] ? TRUE : FALSE;
                }
            }
        }
        error_log("disponibleeeeee".$disponible);
        return $disponible;
    }

    function disponibleReserva($sala, $dia, $fechaInicio, $fechaFin, $horaInicio, $horaFin) {
        $disponible = FALSE;
        error_log("SELECT horario_disponible('$sala', $dia, '$fechaInicio', '$fechaFin','$horaInicio', '$horaFin')");
        if (($rs = UtilConexion::$pdo->query("SELECT horario_disponible('$sala', $dia, '$fechaInicio', '$fechaFin','$horaInicio', '$horaFin')"))) {
            if (($fila = $rs->fetch(PDO::FETCH_ASSOC))) {
                error_log(print_r($fila, 1));
                UtilConexion::getEstado();
                if (isset($fila['horario_disponible'])) {
                    $disponible = $fila['horario_disponible'] ? TRUE : FALSE;
                }
            }
        }
        error_log($disponible);
        return $disponible;
    }

    function usuario($idR) {

        $sql = "select fk_usuario from reserva_sala where id=$idR";
        foreach (UtilConexion::$pdo->query($sql) as $usuarios) {
            $usuario = $usuarios['fk_usuario'];
        }
        return $usuario;
    }

    function getUsuario($idR) {

        $sql = "select fk_usuario from restriccion_calendario where id=$idR";
        foreach (UtilConexion::$pdo->query($sql) as $usuarios) {
            $usuario = $usuarios['fk_usuario'];
        }
        return $usuario;
    }

    function getSala($idR) {

        $sql = "select fk_sala from reserva_sala where id=$idR";
        foreach (UtilConexion::$pdo->query($sql) as $salas) {
            $sala = $salas['fk_usuario'];
        }
        return $sala;
    }

    public function eliminarReservaSala($argumentos) {
        extract($argumentos);
        error_log("observaciones--->" . $observaciones);
        if ($seleccion == 1) {
            $sql = "DELETE FROM reserva_sala  WHERE (fk_sala, actividad, fk_usuario, fk_responsable , TO_CHAR(fecha_inicio,'HH24:MI'),TO_CHAR(fecha_fin,'HH24:MI')) =(select fk_sala, actividad, fk_usuario,fk_responsable,TO_CHAR(fecha_inicio,'HH24:MI'),TO_CHAR(fecha_fin,'HH24:MI') from datos_originalessala($idReserva));";
            error_log($sql);
            UtilConexion::$pdo->exec($sql);
            echo UtilConexion::getEstado();
        } else {
            UtilConexion::$pdo->exec("DELETE FROM reserva_sala WHERE id = $idReserva");
            echo UtilConexion::getEstado();
        }
    }

//    public function eliminarReservaSala($argumentos) {
//        extract($argumentos);
//        $fk_usuario = self::usuario($idReserva);
//        
//     if ($seleccion) {
//            UtilConexion::$pdo->exec("DELETE FROM reserva_sala WHERE id = $idReserva");
//            error_log("solo un evento mas el ok $ok");
//        } else {
//            error_log("eleccion $seleccion");
//            $inicio = new DateTime($start);
//            $fin = new DateTime($end);
//            $horainicio = $inicio->format('H:i:s');
//            $horafin = $fin->format('H:i:s');
//            $interval = dateInterval::createFromDateString('1 day');
//            $fechas = new DatePeriod($inicio, $interval, $fin);
//
//            $sql = "DELETE from reserva_sala where fecha_inicio=? and fecha_fin=? and fk_usuario=? and fk_sala=?";
//            $stmt = UtilConexion::$pdo->prepare($sql);
//            $mensaje = '';
//            $ok = false;
//
//            foreach ($fechas as $fecha) {
//                $fecha = $fecha->format('Y-m-d');
//                $inicio = "$fecha $horainicio";
//                $fin = "$fecha $horafin";
//                $diasemana = date("w", strtotime($fecha));
//                $ok = $stmt->execute(array($inicio, $fin, $fk_usuario, $fk_sala));
//
//                if (!$ok) {
//                    $mensaje .= " - Fallo al eliminar: $inicio - $fin\n";
//                }
//            }
//        }
//        $ok = TRUE;
//        if ($mensaje) {
//            $mensaje = "se prsentaron problemas:\n$mensaje";
//            $ok = FALSE;
//        }
//
//        echo json_encode(['ok' => $ok, 'estado' => $estado]);
//    }
//    public function modificarReservaSala($argumentos) {
//        extract($argumentos);
//        $fk_usuario = self::usuario($idReserva);
//        $fecha_inicio = new DateTime($start);
//        $fecha_fin = new DateTime($end);
//        $inicio = $fecha_inicio->format('Y-m-d H:i:s');
//        $fin = $fecha_fin->format('Y-m-d H:i:s');
//        $mensaje = '';
//        error_log($seleccion);
//        if ($seleccion == 1) {
//            error_log("UPDATE reserva_sala SET  fecha_inicio='$inicio', fecha_fin='$fin', actividad='$actividad', 
//                                      fk_usuario='$usuario', estado=$estado, observaciones='$observaciones', color='$color'
//                                      WHERE id=$idReserva");
//            UtilConexion::$pdo->exec("UPDATE reserva_sala SET  fecha_inicio='$inicio', fecha_fin='$fin', actividad='$actividad', 
//                                      fk_usuario='$usuario', estado=$estado, observaciones='$observaciones', color='$color'
//                                      WHERE id=$idReserva");
//        } else {
//
//            $fi = self::fechaInicio($idReserva);
//            $ff = self::fecha_fin($idReserva);
//            $f_inicio = new DateTime($fi);
//            $f_fin = new DateTime($ff);
//            $inicio = new DateTime($start);
//            $fin = new DateTime($end);
//            $horainicio = $inicio->format('H:i:s');
//            $horafin = $fin->format('H:i:s');
//            $h_inicio = $fecha_inicio->format('H:i:s');
//            $h_fin = $fecha_fin->format('H:i:s');
//            $interval = dateInterval::createFromDateString('1 day');
//            $fechas = new DatePeriod($inicio, $interval, $fin);
//
//            $sql = "UPDATE reserva_sala SET  fecha_inicio= ?, fecha_fin=?,actividad=?, fk_usuario=?, estado=?,
//                     observaciones=?,fk_sala=? color=?
//                                      WHERE fecha_inicio=? and fecha_fin=? and fk_usuario=? ";
//            $stmt = UtilConexion::$pdo->prepare($sql);
//            $ok = TRUE;
//            $mensaje = '';
//
//            foreach ($fechas as $fecha) {
//                $fecha = $fecha->format('Y-m-d');
//                $inicio = "$fecha $horainicio";
//                $fin = "$fecha $horafin";
//                $f_inicio = "$fecha $h_inicio";
//                $f_fin = "$fecha $h_fin";
//                $diaSemana = date("w", strtotime($fecha));
//                $ok = False;
//
//                if (count($dias) == 0) {
//                    $dias[] = $diaSemana;
//                }
//                error_log(print_r(array($f_inicio, $f_fin, $actividad, $usuario, $estado, $observaciones, $fk_sala, $color, $f_inicio, $f_fin, $fk_usuario), 1));
//                if (in_array($diaSemana, $dias)) {
//                    if (!$stmt->execute(array($f_inicio, $f_fin, $actividad, $usuario, $estado, $observaciones, $fk_sala, $color, $f_inicio, $f_fin, $fk_usuario))) {
//                        $ok = False;
//                    }if (!$ok) {
//                        $mensaje .= "Error al actualizar $f_inicio - $f_fin\n";
//                    }
//                }
//            }
//        }
//        $ok = TRUE;
//        if ($mensaje) {
//            $mensaje = "Falló:\n$mensaje";
//            $ok = FALSE;
//        }
//
//        echo json_encode(['ok' => $ok, 'mensaje' => $mensaje]);
//    }


    public function actualizarReservaSala($argumentos) {
        extract($argumentos);
        if ($seleccion == 0) {
            UtilConexion::$pdo->exec("UPDATE reserva_sala SET  actividad='$actividad', fk_usuario='$usuario', estado=$estado, observaciones='$observaciones', fk_responsable= '$fk_responsable' ,color='$color' WHERE id=$idReserva");
            echo UtilConexion::getEstado();
        } else {
            $sql = "UPDATE reserva_sala SET  actividad='$actividad',  fk_usuario='$usuario', estado=$estado, observaciones='$observaciones', fk_responsable= '$fk_responsable' ,color='$color' 
                                        WHERE (fk_sala, actividad, fk_usuario, fk_responsable , TO_CHAR(fecha_inicio,'HH24:MI'),TO_CHAR(fecha_fin,'HH24:MI')) =(select fk_sala, actividad, fk_usuario,fk_responsable,TO_CHAR(fecha_inicio,'HH24:MI'),TO_CHAR(fecha_fin,'HH24:MI') from datos_originalessala($idReserva));";
            error_log("sql--->" . $sql);
            UtilConexion::$pdo->exec($sql);
            echo UtilConexion::getEstado();
        }
    }

    public function modificarRestriccion($argumentos) {
        extract($argumentos);
        $id = $idReserva . substr(5, 1);
        $grupo = $fk_grupo . substr(0, 4);
        $mensaje = '';
        $ok = FALSE;
        error_log($seleccion);
        error_log();
        $sql = "UPDATE restriccion_calendario SET  hora_inicio=?, hora_fin=?, fk_usuario=?, color=?, fk_sala=?, 
                  fk_grupo=?, dia=?, modalidad=? WHERE id=$id";
        $stm = UtilConexion::$pdo->prepare($sql);
        $ok = $stmt->execute(array($start, $end, $fk_usuario, $color, $fk_sala, $grupo, $dia, $modalidad));
        if (!ok) {
            $mensaje = 'fallo la insercion';
            $ok = FALSE;
        }
        $ok = TRUE;
        if ($mensaje) {
            $mensaje = "Falló:\n$mensaje";
            $ok = FALSE;
        }

        echo json_encode(['ok' => $ok, 'mensaje' => $mensaje]);
    }

    function fechaInicio($idReserva) {
        $sql = "select fecha_inicio from reserva_sala where id=$idReserva";
        foreach (UtilConexion::$pdo->query($sql) as $fecha) {
            $fecha_inicio = $fecha['fecha_inicio'];
        }
        return $fecha_inicio;
    }

    function fecha_fin($idReserva) {
        $sql = "select fecha_fin from reserva_sala where id=$idReserva";
        foreach (UtilConexion::$pdo->query($sql) as $fecha) {
            $fecha_fin = $fecha['fecha_inicio'];
        }
        return $fecha_fin;
    }

    public function actualizarHorarioReserva($argumentos) {
        extract($argumentos);
        error_log("UPDATE reserva_sala
                     SET  fecha_inicio='$start', fecha_fin='$end' 
                     WHERE id =$id");
        UtilConexion::$pdo->exec("UPDATE reserva_sala
                     SET  fecha_inicio='$start', fecha_fin='$end' 
                     WHERE id =$id");
        UtilConexion::getEstado();
    }

    /**
     * 
      metodos del jqGrid
     */
    function add($argumentos) {
        extract($argumentos);
        $ok = UtilConexion::$pdo->exec("insert into sala (nombre, capacidad, fk_bloque) values('$nombre', '$capacidad','$bloque')");
        echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "No se pudo agregar el departamento"));
    }

    /**
     * Actualiza una fila.
     * @param <type> $argumentos Un array con el id a buscar y el nuevo tema
     */
    function edit($argumentos) {
        extract($argumentos);
        $ok = UtilConexion::$pdo->exec("UPDATE sala SET nombre='$nombre', capacidad='$capacidad', fk_bloque='$bloque' WHERE nombre='$nombre'");
        echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "Falló la actualización de los datos"));
    }

    /**
     * Elimina las filas cuyos IDs se pasen como argumentos.
     * @param <type> $argumentos los IDs de los departamentos a ser eliminados.
     * $argumentos es un cadena que contiene uno o varios números separados por
     * comas, que corresponden a los IDs de las filas a eliminar.
     */
    function del($argumentos) {
        $datos = "" . $argumentos['id'] . "";
        $ok = UtilConexion::$pdo->exec("DELETE FROM sala WHERE nombre='$datos'");
        echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "Falló la eliminación"));
    }

    function select($argumentos) {
        $where = UtilConexion::getWhere($argumentos);
        extract($argumentos);
        error_log("SELECT nombre FROM sala $where");

        if (isset($id)) {
            $where = "WHERE fk_bloque = '$id'";
        } else {
            $where = "WHERE fk_bloque = 'ninguno'";
        }
        error_log("SELECT nombre, fk_bloque FROM sala $where");

        $count = UtilConexion::$pdo->query("SELECT nombre FROM sala $where")->rowCount();
        if ($count > 0) {
            $total_pages = ceil($count / $rows);
        } else {
            $total_pages = 0;
        }


        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $start = $rows * $page - $rows;
        if ($start < 0) {
            $start = 0;
        }

        $respuesta = [
            'total' => $total_pages,
            'page' => $page,
            'records' => $count
        ];

        $sql = "SELECT s.nombre nombre, s.capacidad capacidad , s.fk_bloque bloque,bl.fk_sede sede FROM sala s inner join bloque bl on s.fk_bloque=bl.nombre  $where ORDER BY $sidx $sord LIMIT $rows OFFSET $start";
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['nombre'],
                'cell' => [$fila['nombre'], $fila['capacidad'], $fila['bloque'], $fila['sede']]
            ];
        }
        error_log(print_r($respuesta, 1));
        echo json_encode($respuesta);
    }

}

?>
