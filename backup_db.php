<?php
include 'includes/session.php';
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

require_once 'includes/dropbox_helper.php';

$host = 'localhost';
$db   = 'u357979451_cooperativa';
$user = 'u357979451_oscar';
$pass = 'Dd?P/ZUFg?94';

$fecha = date('Y-m-d_H-i-s');
$backupDir = __DIR__ . "/BACKUP_DATABASE";

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$archivoLocal = $backupDir . "/respaldo_$fecha.sql";
$archivoDropbox = "/respaldo_total/base_datos/respaldo_$fecha.sql";

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    $_SESSION['error'] = "Error de conexión a la base de datos: " . $mysqli->connect_error;
    header('Location: folders.php');
    exit();
}

// Función para exportar la estructura y datos de una tabla
function exportTable($mysqli, $table) {
    $sql = "";

    // Obtener estructura
    $res = $mysqli->query("SHOW CREATE TABLE `$table`");
    if (!$res) return false;
    $row = $res->fetch_assoc();
    $sql .= "-- Estructura de tabla para `$table`\n";
    $sql .= "DROP TABLE IF EXISTS `$table`;\n";
    $sql .= $row['Create Table'] . ";\n\n";

    // Obtener datos
    $res = $mysqli->query("SELECT * FROM `$table`");
    if (!$res) return false;

    $numRows = $res->num_rows;
    if ($numRows > 0) {
        $sql .= "-- Datos para la tabla `$table`\n";
        $sql .= "INSERT INTO `$table` VALUES \n";

        $valuesArr = [];
        while ($row = $res->fetch_assoc()) {
            $vals = array_map(function($val) use ($mysqli) {
                if (is_null($val)) return "NULL";
                return "'" . $mysqli->real_escape_string($val) . "'";
            }, array_values($row));

            $valuesArr[] = "(" . implode(", ", $vals) . ")";
        }
        $sql .= implode(",\n", $valuesArr) . ";\n\n";
    }
    return $sql;
}

$sqlBackup = "-- Respaldo generado el $fecha\n\n";
$sqlBackup .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

$tablesRes = $mysqli->query("SHOW TABLES");
if (!$tablesRes) {
    $_SESSION['error'] = "No se pudieron obtener las tablas.";
    header('Location: folders.php');
    exit();
}

while ($tableRow = $tablesRes->fetch_array()) {
    $tableName = $tableRow[0];
    $tableSql = exportTable($mysqli, $tableName);
    if ($tableSql === false) {
        $_SESSION['error'] = "Error exportando la tabla $tableName";
        header('Location: folders.php');
        exit();
    }
    $sqlBackup .= $tableSql;
}

$sqlBackup .= "SET FOREIGN_KEY_CHECKS=1;\n";

$mysqli->close();

// Guardar backup en archivo
if (file_put_contents($archivoLocal, $sqlBackup) === false) {
    $_SESSION['error'] = "No se pudo escribir el archivo de respaldo.";
    header('Location: folders.php');
    exit();
}

// Subir archivo a Dropbox
try {
    $accessToken = obtenerAccessToken();
    $app = new \Kunnu\Dropbox\DropboxApp(DROPBOX_CLIENT_ID, DROPBOX_CLIENT_SECRET, $accessToken);
    $dropbox = new \Kunnu\Dropbox\Dropbox($app);

    $dropbox->upload($archivoLocal, $archivoDropbox, ['autorename' => true]);

    // Eliminar archivo local temporal
    unlink($archivoLocal);

    $_SESSION['success'] = "Respaldo manual guardado en Dropbox exitosamente.";
} catch (Exception $e) {
    $_SESSION['error'] = "Error al subir respaldo a Dropbox: " . $e->getMessage();
}

header('Location: folders.php');
exit();
