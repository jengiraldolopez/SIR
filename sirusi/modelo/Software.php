<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Software
 *
 * @author Alejo
 */
class Software implements Persistible{

    /**
     * Inserta una nueva fila enviada por $_POST
     * @param <type> $argumentos El array que contiene los argumentos enviados por $_POST
     */
    function add($argumentos) {
        extract($argumentos);
        $ok = UtilConexion::$pdo->exec("INSERT INTO software(nombre, version)VALUES ('$nombre','$version')");
        echo UtilConexion::getEstado();
    }

    /**
     * Elimina las filas cuyos IDs se pasen como argumentos.
     * @param <type> $argumentos los IDs de las salas a ser eliminados.
     * $argumentos es un cadena que contiene uno o varios números separados por
     * comas, que corresponden a los IDs de las filas a eliminar.
     */
    
     function del($argumentos) {
        extract($argumentos);
        $ok = UtilConexion::$pdo->exec("DELETE FROM software WHERE id=$id");
        echo UtilConexion::getEstado();
    }

    /**
     * Actualiza una fila.
     * @param <type> $argumentos Un array con el id a buscar y el nuevo tema
     */
     function edit($argumentos) {
        extract($argumentos);
        $ok = UtilConexion::$pdo->exec("UPDATE software SET id='$id', nombre='$nombre',version='$version' where id='$id'");
        echo UtilConexion::getEstado();
    }

    public function select($argumentos) {
     $where = UtilConexion::getWhere($argumentos); // Se construye la clausula WHERE
        if ($where) {
            $where = $where . " AND id <> 0";
        } else {
            $where = " WHERE id <> 0 ";
        }
        extract($argumentos);
        error_log("SELECT * FROM software $where");
        $count = UtilConexion::$pdo->query("SELECT * FROM software $where")->rowCount();
        
   
  
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

        $sql = "SELECT * FROM software $where ";
        
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['id'],
                'cell'=> [$fila['nombre'],$fila['version']]
            ];
        }
        // Quite los comentarios para ver el array original y el array codificado en JSON
//        error_log(print_r($respuesta, TRUE));
//        error_log(print_r(json_encode($respuesta), TRUE));
        echo json_encode($respuesta);
    }
    
    public function getLista() {
        $filas['0'] = 'Seleccione un Software';
        $filas += UtilConexion::$pdo->query("SELECT id_serial, nombre FROM software ORDER BY nombre")->fetchAll(PDO::FETCH_KEY_PAIR);
        echo json_encode($filas);
    }
    
    public function getSelect() {
        $select = "<select>";
        $select .= "<option value='0'>Seleccione un software</option>";
        foreach (UtilConexion::$pdo->query("SELECT id FROM software ORDER BY id") as $fila) {
            $select .= "<option value='{$fila['id']}'>{$fila['id']}</option>";
        }
        echo ($select . "</select>");
    }

    
}
