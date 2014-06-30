<?php

/**
 * Description of Conexion:
 * Implementación del patrón Singleton para proporcionar una única instancia de esta clase
 * que será la encargada de proporcionar la conexión a la base de datos.
 * Como puede verse, uno de los métodos públicos es:
 *    getInstance: que devuelve un objeto de tipo Conexión y
 * El constructor y el método __clone() son privados para evitar su uso fuera de la clase.
 * @author Carlos Cuesta Iglesias
 */
class UtilConexion {

    public static $pdo;                     // Una referencia a un objeto de tipo PDO (PHP Data Object)
    public static $propiedadesConexion;
    private static $conexion;
    private static $comspec;                // Procesador de comandos (En Win7 C:\Windows\system32\cmd.exe)
    private static $rutaAplicacion;
    private static $rutaRaiz;
     public static $rutaDescargas;

    /**
     * Es posible que un script envié varios mensajes getInstance(...) a un objeto de tipo Conexion,
     * sinembargo siempre se retornará la misma instancia de Conexión, garantizando así la
     * implementacion del Patrón Singleton
     * @param <type> $driver El tipo de driver: postgres, mysql, etc.
     * @param <type> $servidor El host: localhost o cualquier IP válida
     * @param <type> $usuario El usuario que tiene privilegios de acceso a la base de datos
     * @param <type> $clave La clave del usuario
     * @return <type> Una instancia de tipo Conexion
     */
    public static function inicializar() {
        self::$comspec = $_SERVER['COMSPEC'];
        self::$rutaRaiz = $_SERVER['DOCUMENT_ROOT']; // "C:/wamp/www/";
        self::$rutaAplicacion = self::$rutaRaiz . "sirusi/"; // "C:/wamp/www/sirusi/";
        self::$rutaDescargas=DOCUMENT_ROOT."siremboxIO/Down/";

        $baseDeDatos = 'sirusi';
        $servidor = 'localhost';  // 127.0.0.1:80
        $puerto = '5432';  // puerto postgres
        $usuario = 'postgres';
        $contrasena = 'admin';  ///////////////////  OJO  //////////////////////
        try {
            self::$pdo = new PDO("pgsql:host=$servidor port=$puerto dbname=$baseDeDatos", $usuario, $contrasena);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * Se sobreescribe este 'método mágico' para evitar que se creen clones de esta clase
     */
    private function __clone() { /* ... */
    }

    /* ---------------------------  Inicio de las funciones para construir la cláusula WHERE  ---------------------------- */

    /**
     * Construye una cláusula WHERE a partir de los argumentos enviados por un jqGrid.
     * Ver el original en los demos de http://www.trirand.com/blog/
     * @param type $argumentos Un array asociativo que contiene los parámetros enviados por jqGrid 
     * @return string Una cláusula WHERE
     */
    public static function getWhere($argumentos) {
        $where = "";
        if ($argumentos['_search'] == 'true') {
            $json = json_decode(stripslashes($argumentos['filters']));
            $where = " WHERE" . self::getClausulaWhere($json);
        }
        return $where;
    }

    private static function getClausulaWhere($json) {
        foreach ($json->rules as $g) {
            $constraint = $json->groupOp;
            if (isset($where)) {  // no inicializar, fallaría el algoritmo
                $where .= " $constraint ";
            } else {
                $where = " ";
            }
            if ($g->op == "eq") {
                $where .= $g->field . " = '$g->data'";
            } elseif ($g->op == "ne") {
                $where .= $g->field . " <> '$g->data'";
            } elseif ($g->op == "lt") {
                $where .= $g->field . " < '$g->data'";
            } elseif ($g->op == "le") {
                $where .= $g->field . " <= '$g->data'";
            } elseif ($g->op == "gt") {
                $where .= $g->field . " > '$g->data'";
            } elseif ($g->op == "ge") {
                $where .= $g->field . " >= '$g->data'";
            } elseif ($g->op == "bw") { // empieza por
                $where .= $g->field . " ILIKE '$g->data%'";
            } elseif ($g->op == "bn") {// no empieza por
                $where .= $g->field . " NOT ILIKE '$g->data%'";
            } elseif ($g->op == "in") {// incluido entre
                $where .= $g->field . " ILIKE '$g->data'";
            } elseif ($g->op == "ni") {
                $where .= $g->field . " NOT ILIKE '$g->data'";
            } elseif ($g->op == 'ew') {// finaliza con
                $where .= $g->field . " ILIKE '%$g->data'";
            } elseif ($g->op == "en") {// no finaliza con
                $where .= $g->field . " NOT ILIKE '%$g->data'";
            } elseif ($g->op == "cn") {// contiene
                $where .= $g->field . " ILIKE '%$g->data%'";
            } elseif ($g->op == "nc") {// no contiene
                $where .= $g->field . " NOT ILIKE '%$g->data%'";
            }
        }
        if (!isset($where)) {
            $where = '';
        }
        if (isset($json->groups)) {
            $count = count($json->groups);
            for ($i = 0; $i < $count; $i++) {
                if (($tmp = self::getClausulaWhere($json->groups[$i]))) {
                    $where .= " " . $constraint . " " . $tmp;
                }
            }
        }
        return $where;
    }

    /* ---------------------------  Fin de las funciones para construir la cláusula WHERE  ---------------------------- */

    public static function getEstado($json = TRUE) { //
        error_log('¡Pilas! ' . print_r(self::$pdo->errorInfo(), TRUE));
        if (!($ok = !(self::$pdo->errorInfo()[1]))) {
            error_log('¡Pilas! ' . print_r(self::$pdo->errorInfo(), TRUE));
        }
        $mensaje = '';
        if (count($errorInfo = explode("\n", self::$pdo->errorInfo()[2])) > 1) {
            $mensaje = substr($errorInfo[1], 9);
        }
        return $json ? json_encode(['ok' => $ok, 'mensaje' => $mensaje]) : ['ok' => $ok, 'mensaje' => $mensaje];
    }

}

?>
