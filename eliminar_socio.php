<?php
include 'baseDatos/conexion.php';

if (isset($_GET['id'])) {
    $id_socio = $_GET['id'];

    // Consulta para eliminar el socio
    $sql = "DELETE FROM socios WHERE id_socio = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_socio]);

    // Redirigir a la página principal después de eliminar
    header("Location: socios.php");
    exit();
} else {
    // Redirigir si no se pasa el id del socio
    header("Location: socios.php");
    exit();
}
?>
