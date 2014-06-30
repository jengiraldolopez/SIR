<?php

class Usuario {

    public function autenticar($argumentos) {
        $opciones = [];
        extract($argumentos);
        $autenticacion = UtilConexion::$pdo->query("SELECT * FROM login WHERE usuario = '$usuario'")->fetch();
        $password = $autenticacion['clave'];

        $filaClave = UtilConexion::$pdo->query("SELECT nombre, apellido, UPPER(SUBSTRING(TRIM(nombre) FROM 1 FOR 2))||TRIM(codigo) " . "clave" . " FROM usuario WHERE codigo LIKE '$usuario'")->fetch();
        $claveUsuario = md5($filaClave['clave']);

        if ($usuario == $autenticacion['usuario'] && $contrasena == $claveUsuario) {
            $opciones = ['Restablecer' => 'Elemento para pedir restablecimiento de la contraseña'];
        } else if ($usuario == $autenticacion['usuario'] & $contrasena == $password) {
            $varau = $autenticacion['fk_rol'];
            if ($varau == 6) {
                $opciones = [
                    'Asignación' => "vista/html/monitorias-asignacion.html",
                    'Cerrar sesión' => '<-- este elemento es imprescindible, escrito exactamente así',
                    'Comunidad' => "vista/html/comunidad.html",
                    'Configuración' => "vista/html/configuracion.html",
                    'Control' => "vista/html/monitorias-control.html",
                    'Programación' => "vista/html/monitorias-programacion.html",
                    'Detalle de equipos' => "vista/html/equipos-detalle.html",
                    'Detalle de salas' => "vista/html/salas-detalle.html",
                    'Mantenimiento' => "vista/html/monitorias.html",
                    'Privilegios' => "vista/html/privilegios.html",
                    'Reportes' => "vista/html/reportes.html",
                    'Reserva de equipos' => "vista/html/equipos-reserva.html",
                    'Reserva de salas' => "vista/html/salas-reserva.html",
                    'Utilidades' => "vista/html/utilidades.html"
                ];
            } else {
                $opciones = [
                    'Asignación' => "vista/html/monitorias-asignacion.html",
                    'Cerrar sesión' => '<-- este elemento es imprescindible, escrito exactamente así',
                    'Comunidad' => '',
                    'Configuración' => '',
                    'Control' => "vista/html/monitorias-control.html",
                    'Detalle de equipos' => "vista/html/equipos-detalle.html",
                    'Detalle de salas' => "vista/html/salas-detalle.html",
                    'Mantenimiento' => '',
                    'Privilegios' => '',
                    'Reportes' => '',
                    'Reserva de equipos' => "vista/html/equipos-reserva.html",
                    'Reserva de salas' => "vista/html/salas-reserva.html",
                    'Utilidades' => "vista/html/utilidades.html"
                ];
            }
        }
        $nombreUsuario = $filaClave['nombre'] . ' ' . $filaClave['apellido'];
        echo json_encode(['usuarioID' => $usuario, 'usuarioNombre' => $nombreUsuario, 'opciones' => $opciones]);
    }

    public function restablecer($argumentos) {
        extract($argumentos);
        $sql1 = "SELECT clave FROM login WHERE clave = '$claveAnterior'";
        $cons1 = UtilConexion::$pdo->query($sql1)->fetch();
        $pass = $cons1['clave'];
        if ($pass == $claveAnterior & $claveNueva == $claveConfirmada) {
            $sql2 = "UPDATE login SET clave = '$claveNueva' WHERE clave = '$claveAnterior'";
            $cons2 = UtilConexion::$pdo->query($sql2)->fetch();
            $opciones = true;
        } else {
            $opciones = false;
        }
        echo json_encode($opciones);
    }

    function add($argumentos) {
        extract($argumentos);
        switch ($argumentos['paramAdd']) {
            case 'estudiante':
                UtilConexion::$pdo->exec("INSERT INTO usuario VALUES('$codigo', '$nombre', '$apellido', '$telefono', '$email', 1, $dependencia, true)");
            case 'docente':
                UtilConexion::$pdo->exec("INSERT INTO usuario VALUES('$codigo', '$nombre', '$apellido', '$telefono', '$email', 4, $dependencia, true)");
            case 'administrativo':
                UtilConexion::$pdo->exec("INSERT INTO usuario VALUES('$codigo', '$nombre', '$apellido', '$telefono', '$email', 0, $dependencia, true)");
            case 'invitado':
                UtilConexion::$pdo->exec("INSERT INTO usuario VALUES('$codigo', '$nombre', '$apellido', '$telefono', '$email', $rol, $dependencia, true)");
            case 'privilegio':
                UtilConexion::$pdo->exec("INSERT INTO usuario VALUES('$codigo', '$nombre', '$apellido', '$telefono', '$email', $rol, $dependencia, true)");
                $sql = "SELECT UPPER(SUBSTRING(TRIM(nombre) FROM 1 FOR 2))||TRIM(codigo) " . "clave" . " FROM usuario WHERE codigo LIKE '$codigo'";
                $cons = UtilConexion::$pdo->query($sql)->fetch();
                $clave = md5($cons['clave']);
                UtilConexion::$pdo->exec("INSERT INTO login VALUES('$codigo', '$clave', $rol)");
        }
        echo UtilConexion::getEstado();
    }

    function edit($argumentos) {
        extract($argumentos);
        if ($argumentos['paramEdit'] == 'invitado' | 'privilegio') {
            UtilConexion::$pdo->exec("UPDATE usuario SET fk_rol = '$rol', codigo = '$codigo', nombre = '$nombre', apellido = '$apellido', telefono = '$telefono', email = '$email', fk_dependencia = '$dependencia' WHERE codigo = '$codigo'");
        } else {
            UtilConexion::$pdo->exec("UPDATE usuario SET codigo = '$codigo', nombre = '$nombre', apellido = '$apellido', telefono = '$telefono', email = '$email', fk_dependencia = '$dependencia' WHERE codigo = '$codigo'");
        }
        echo UtilConexion::getEstado();
    }

    function modifyPass($argumentos) {
        extract($argumentos);
        $sql1 = "SELECT UPPER(SUBSTRING(TRIM(nombre) FROM 1 FOR 2))||TRIM(codigo) " . "clave" . " FROM usuario WHERE codigo LIKE '$codigo'";
        $cons1 = UtilConexion::$pdo->query($sql1)->fetch();
        $clave = md5($cons1['clave']);
        UtilConexion::$pdo->exec("UPDATE login SET clave = '$clave' WHERE usuario = '$codigo'");
        echo UtilConexion::getEstado();
    }

    function del($argumentos) {
        extract($argumentos);
        UtilConexion::$pdo->exec("DELETE FROM usuario WHERE codigo = '$id'");
        echo UtilConexion::getEstado();
    }

    /**
     * @param type $argumentos
     */
    /*     * *********************************************************************************************************************************************************************** */

    function selectEstudiantes($argumentos) {
        $where = UtilConexion::getWhere($argumentos); // Se construye la clausula WHERE
        if ($where) {
            $where = $where . " WHERE u.fk_dependencia=d.id AND u.fk_rol='1'";
        } else {
            $where = " WHERE u.fk_dependencia=d.id AND u.fk_rol='1'";
        }
        extract($argumentos);
        $count = UtilConexion::$pdo->query("SELECT DISTINCT u.codigo, u.nombre, u.apellido, u.telefono, u.email, d.nombre " . "dependencia" . " FROM usuario u, dependencia d, rol r $where")->rowCount();
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

        $sql = "SELECT DISTINCT u.codigo, u.nombre, u.apellido, u.telefono, u.email, d.nombre " . "dependencia" . " FROM usuario u, dependencia d, rol r $where ORDER BY $sidx $sord LIMIT $rows OFFSET $start";
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['codigo'],
                'cell' => [$fila['codigo'], $fila['nombre'], $fila['apellido'], $fila['telefono'], $fila['email'], $fila['dependencia']]
            ];
        }
        // Quite los comentarios para ver el array original y el array codificado en JSON
        // error_log(print_r($respuesta, TRUE));
        // error_log(print_r(json_encode($respuesta), TRUE));
        echo json_encode($respuesta);
    }

    /*     * *********************************************************************************************************************************************************************** */

    function selectDocentes($argumentos) {
        $where = UtilConexion::getWhere($argumentos); // Se construye la clausula WHERE
        if ($where) {
            $where = $where . " WHERE u.fk_dependencia=d.id AND u.fk_rol='4'";
        } else {
            $where = " WHERE u.fk_dependencia=d.id AND u.fk_rol='4'";
        }
        extract($argumentos);
        $count = UtilConexion::$pdo->query("SELECT DISTINCT u.codigo, u.nombre, u.apellido, u.telefono, u.email, d.nombre " . "dependencia" . " FROM usuario u, dependencia d, rol r $where")->rowCount();
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

        $sql = "SELECT DISTINCT u.codigo, u.nombre, u.apellido, u.telefono, u.email, d.nombre " . "dependencia" . " FROM usuario u, dependencia d, rol r $where ORDER BY $sidx $sord LIMIT $rows OFFSET $start";
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['codigo'],
                'cell' => [$fila['codigo'], $fila['nombre'], $fila['apellido'], $fila['telefono'], $fila['email'], $fila['dependencia']]
            ];
        }
        // Quite los comentarios para ver el array original y el array codificado en JSON
        // error_log(print_r($respuesta, TRUE));
        // error_log(print_r(json_encode($respuesta), TRUE));
        echo json_encode($respuesta);
    }

    /*     * *********************************************************************************************************************************************************************** */

    function selectAdministrativos($argumentos) {
        $where = UtilConexion::getWhere($argumentos); // Se construye la clausula WHERE
        if ($where) {
            $where = $where . " WHERE u.fk_dependencia=d.id AND u.fk_rol='0'";
        } else {
            $where = " WHERE u.fk_dependencia=d.id AND u.fk_rol='0'";
        }
        extract($argumentos);
        $count = UtilConexion::$pdo->query("SELECT DISTINCT u.codigo, u.nombre, u.apellido, u.telefono, u.email, d.nombre " . "dependencia" . " FROM usuario u, dependencia d, rol r $where")->rowCount();
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

        $sql = "SELECT DISTINCT u.codigo, u.nombre, u.apellido, u.telefono, u.email, d.nombre " . "dependencia" . " FROM usuario u, dependencia d, rol r $where ORDER BY $sidx $sord LIMIT $rows OFFSET $start";
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['codigo'],
                'cell' => [$fila['codigo'], $fila['nombre'], $fila['apellido'], $fila['telefono'], $fila['email'], $fila['dependencia']]
            ];
        }
        // Quite los comentarios para ver el array original y el array codificado en JSON
        // error_log(print_r($respuesta, TRUE));
        // error_log(print_r(json_encode($respuesta), TRUE));
        echo json_encode($respuesta);
    }

    /*     * *********************************************************************************************************************************************************************** */

    function selectInvitados($argumentos) {
        $where = UtilConexion::getWhere($argumentos); // Se construye la clausula WHERE
        if ($where) {
            $where = $where . " WHERE u.fk_dependencia=d.id AND u.fk_rol IN (3, 5) AND u.fk_rol = r.id";
        } else {
            $where = " WHERE u.fk_dependencia=d.id AND u.fk_rol IN (3, 5) AND u.fk_rol = r.id";
        }
        extract($argumentos);
        $count = UtilConexion::$pdo->query("SELECT DISTINCT u.codigo, u.nombre, u.apellido, u.telefono, u.email, r.nombre " . "rol" . ", d.nombre " . "dependencia" . " FROM usuario u, dependencia d, rol r $where")->rowCount();
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

        $sql = "SELECT DISTINCT u.codigo, u.nombre, u.apellido, u.telefono, u.email, r.nombre " . "rol" . ", d.nombre " . "dependencia" . " FROM usuario u, dependencia d, rol r $where ORDER BY $sidx $sord LIMIT $rows OFFSET $start";
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['codigo'],
                'cell' => [$fila['codigo'], $fila['nombre'], $fila['apellido'], $fila['telefono'], $fila['email'], $fila['rol'], $fila['dependencia']]
            ];
        }
        // Quite los comentarios para ver el array original y el array codificado en JSON
        // error_log(print_r($respuesta, TRUE));
        // error_log(print_r(json_encode($respuesta), TRUE));
        echo json_encode($respuesta);
    }

    /*     * *********************************************************************************************************************************************************************** */

    function selectAdminpass($argumentos) {
        $where = UtilConexion::getWhere($argumentos); // Se construye la clausula WHERE
        if ($where) {
            $where = $where . " WHERE u.fk_dependencia=d.id AND u.fk_rol IN (2, 6) AND u.fk_rol = r.id";
        } else {
            $where = " WHERE u.fk_dependencia=d.id AND u.fk_rol IN (2, 6) AND u.fk_rol = r.id";
        }
        extract($argumentos);
        $count = UtilConexion::$pdo->query("SELECT DISTINCT u.codigo, u.nombre, u.apellido, u.telefono, u.email, r.nombre " . "rol" . ", d.nombre " . "dependencia" . " FROM usuario u, dependencia d, rol r $where")->rowCount();
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

        $sql = "SELECT DISTINCT u.codigo, u.nombre, u.apellido, u.telefono, u.email, r.nombre " . "rol" . ", d.nombre " . "dependencia" . " FROM usuario u, dependencia d, rol r $where ORDER BY $sidx $sord LIMIT $rows OFFSET $start";
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'id' => $fila['codigo'],
                'cell' => [$fila['codigo'], $fila['nombre'], $fila['apellido'], $fila['telefono'], $fila['email'], $fila['rol'], $fila['dependencia']]
            ];
        }
        // Quite los comentarios para ver el array original y el array codificado en JSON
        // error_log(print_r($respuesta, TRUE));
        // error_log(print_r(json_encode($respuesta), TRUE));
        echo json_encode($respuesta);
    }

    public function getSelectD() {
        $select = "<select>";
        $select .= "<option value='0'>Seleccione una dependencia</option>";
        foreach (UtilConexion::$pdo->query("SELECT id, nombre FROM dependencia ORDER BY nombre") as $fila) {
            $select .= "<option value='{$fila['id']}'>{$fila['nombre']}</option>";
        }
        echo ($select . "</select>");
    }

    public function getSelectR() {
        $select = "<select>";
        $select .= "<option value='0'>Seleccione un Rol</option>";
        foreach (UtilConexion::$pdo->query("SELECT id, nombre FROM rol ORDER BY nombre") as $fila) {
            $select .= "<option value='{$fila['id']}'>{$fila['nombre']}</option>";
        }
        echo ($select . "</select>");
    }

    public function getSelectRI() {
        $select = "<select>";
        $select .= "<option value='0'>Seleccione un Rol</option>";
        foreach (UtilConexion::$pdo->query("SELECT id, nombre FROM rol WHERE id IN (3, 5)ORDER BY nombre") as $fila) {
            $select .= "<option value='{$fila['id']}'>{$fila['nombre']}</option>";
        }
        echo ($select . "</select>");
    }

    public function getSelectRA() {
        $select = "<select>";
        $select .= "<option value='0'>Seleccione un Rol</option>";
        foreach (UtilConexion::$pdo->query("SELECT id, nombre FROM rol WHERE id IN (2, 6)ORDER BY nombre") as $fila) {
            $select .= "<option value='{$fila['id']}'>{$fila['nombre']}</option>";
        }
        echo ($select . "</select>");
    }

    public function verificarEstado() {
        error_log('Usuario->verificarEstado');
        echo 'OK';
    }

    public function cerrarSesion() {
        session_destroy();
        $parametros_cookies = session_get_cookie_params();
        setcookie(session_name(), 0, 1, $parametros_cookies["path"]);
    }

    public function getSelect($argumentos) {
        $id = 'cbo' . rand(0, 99999);
        extract($argumentos);
        $select = "<select id='$id'>";
        $select .= "<option value='0'>Seleccione un usuario</option>";
        foreach (UtilConexion::$pdo->query("SELECT codigo, nombre ||' '|| apellido AS nombre_usuario FROM usuario") as $fila) {
            $select .= "<option value='{$fila['codigo']}'>{$fila['nombre_usuario']}</option>";
        }
        $select .= "</select>";
        echo tipoRetorno == 'json' ? json_encode($select) : ($select . "</select>");
    }

}

?>