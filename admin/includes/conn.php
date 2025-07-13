<?php
$host = 'CooperativaLtda.infinityfreeapp.com'; // Reemplazá con el hostname real que ves en el panel
$db   = 'if0_39464143_cooperativa'; // Reemplazá con el nombre completo de tu BD
$user = 'if0_39464143'; // Reemplazá con tu usuario de la base de datos
$pass = 'g8VCJB4WEf'; // Escribí tu contraseña real

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}
?>
