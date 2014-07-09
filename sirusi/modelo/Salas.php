<?php

/**
 * Description of Departamento
 * Las instancias de esta clase se conectan con la base de datos a traves de
 * una referencia que se tiene de PDO.
 * @author tatan313
 */
class Salas implements Persistible {

    /**
     * Inserta una nueva fila enviada por $_POST
     * @param <type> $argumentos El array que contiene los argumentos enviados por $_POST
     */
    function add($argumentos) {
        extract($argumentos);
        // Observe que se están usando procedimientos almacenados, se hubiera podido usar directamente un INSERT ...
        $ok = UtilConexion::$pdo->exec("SELECT departamento_insertar('$id', '$nombre')");
        echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "No se pudo agregar el departamento"));
    }

    /**
     * Actualiza una fila.
     * @param <type> $argumentos Un array con el id a buscar y el nuevo tema
     */
    function edit($argumentos) {
        extract($argumentos);
        $ok = UtilConexion::$pdo->exec("SELECT departamento_actualizar('$id', '$id', '$nombre')");
        echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "Falló la actualización de los datos"));
    }

    /**
     * Elimina las filas cuyos IDs se pasen como argumentos.
     * @param <type> $argumentos los IDs de los departamentos a ser eliminados.
     * $argumentos es un cadena que contiene uno o varios números separados por
     * comas, que corresponden a los IDs de las filas a eliminar.
     */
    function del($argumentos) {
        $datos = "'{" . $argumentos['id'] . "}'";
        $ok = UtilConexion::$pdo->exec("select departamento_eliminar($datos)");
        echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "Falló la eliminación"));
    }

    /**
     * Devuelve los datos necesarios para construir una tabla dinámica.
     * @param <type> $argumentos los argumentos enviados por:
     *               Departamento.js.crearTablaDepartamento()
     */
    function select($argumentos) {
        $where = UtilConexion::getWhere($argumentos); // Se construye la clausula WHERE
        if ($where) {
            $where = $where . " AND id <> '0'";
        } else {
            $where = " WHERE id <> '0'";
        }
        extract($argumentos);
        $count = UtilConexion::$pdo->query("SELECT id FROM departamento_select $where")->rowCount();
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

        $sql = "SELECT * FROM departamento_select $where ORDER BY $sidx $sord LIMIT $rows OFFSET $start";
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['id'],
                'cell' => [$fila['id'], $fila['nombre']]
            ];
        }
        // Quite los comentarios para ver el array original y el array codificado en JSON
        // error_log(print_r($respuesta, TRUE));
        // error_log(print_r(json_encode($respuesta), TRUE));
        error_log(print_r($respuesta, true));
        echo json_encode($respuesta);
    }

    /**
     * Devuelve un array asociativo de la forma: {"id1":"Dato1", "id2":"Dato2", ...,"idN":"DatoN"}
     */
    public function getLista() {
        $filas['0'] = 'Seleccione un departamento';
        $filas += UtilConexion::$pdo->query("SELECT id, nombre FROM departamento_select ORDER BY nombre")->fetchAll(PDO::FETCH_KEY_PAIR);
        error_log(print_r($filas, true));
        echo json_encode($filas);
    }
    
       public function getSelect($argumentos) {
        //extract($argumentos);
           extract($argumentos);
        error_log("holaaaaaaaaaaa");
        $select = "<label>Sala :</label><select id='$id'>";
        $select .= "<option value='0'>Seleccione una sala</option>";
        foreach (UtilConexion::$pdo->query("select distinct(cod_sala) , fk_bloque from salas s , programacion_salas p
                                            where s.cod_sala not in (select fk_cod_sala from programacion_salas
                                            where to_char(fecha_fin,'DD-MM-YYYY')>='$fecha'
                                            and to_char(fecha_fin,'HH24:mi')>='$hora')") as $fila) {
            $select .= "<option value='{$fila['cod_sala']}'> Sala {$fila['cod_sala']} - {$fila['fk_bloque']}</option>";
        }
        //error_log($select);
        echo json_encode($select . "</select><label>   Monitor :</label>
                <input type='text' name='Monitor' value='James Arias' class='diasS'>");
    }
    
    
}

?>
