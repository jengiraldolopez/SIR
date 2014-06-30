<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Bloque
 *
 * @author Alejo
 */
class Bloque implements Persistible {

    /**
     * Inserta una nueva fila enviada por $_POST
     * @param <type> $argumentos El array que contiene los argumentos enviados por $_POST
     */
    function add($argumentos) {
        extract($argumentos);
        $ok = UtilConexion::$pdo->exec("INSERT INTO bloque(nombre,fk_sede) values ('$nombre','$fk_sede')");
        echo UtilConexion::getEstado();
 }

    /**
     * Actualiza una fila.
     * @param <type> $argumentos Un array con el id a buscar y el nuevo tema
     */
    function edit($argumentos) {
        extract($argumentos);
        $ok = UtilConexion::$pdo->exec("UPDATE bloque SET nombre='$nombre',fk_sede='$fk_sede' where nombre='$id'");
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
        $ok = UtilConexion::$pdo->exec("DELETE FROM bloque WHERE nombre='$id'");
        echo UtilConexion::getEstado();

    }

    /**
     * Devuelve los datos necesarios para construir una tabla dinámica.
     * @param <type> $argumentos los argumentos enviados por:
     * Ciudad.js.crearTablaCiudades()
     */
    function select($argumentos) {
        $where = UtilConexion::getWhere($argumentos); // Se construye la clausula WHERE
        extract($argumentos);
       
        if (isset($id)) {
            $where = "WHERE fk_sede = '$id'";
        } else {
            $where = "WHERE fk_sede = 'ninguno'";
        }
        error_log("SELECT nombre, fk_sede FROM bloque $where");
        $count = UtilConexion::$pdo->query("SELECT nombre, fk_sede FROM bloque $where")->rowCount();



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

        $sql = "SELECT * FROM bloque $where ORDER BY $sidx $sord LIMIT $rows OFFSET $start";

        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['nombre'],
                'cell' => [$fila['nombre'], $fila['fk_sede']]
            ];
        }
        // Quite los comentarios para ver el array original y el array codificado en JSON
//        error_log(print_r($respuesta, TRUE));
//        error_log(print_r(json_encode($respuesta), TRUE));
        echo json_encode($respuesta);
    }


    public function getLista() {
        $filas['0'] = 'Seleccione el bloque';
        $filas += UtilConexion::$pdo->query("SELECT nombre, nombre FROM bloque ORDER BY nombre")->fetchAll(PDO::FETCH_KEY_PAIR);
        echo json_encode($filas);
    }
    
    public function getSelect() {
        $select = "<select>";
        $select .= "<option value='0'>Seleccione un bloque</option>";
        foreach (UtilConexion::$pdo->query("SELECT nombre FROM bloque ORDER BY nombre") as $fila) {
            $select .= "<option value='{$fila['nombre']}'>{$fila['nombre']}</option>";
        }
        echo ($select . "</select>");
    }

}
