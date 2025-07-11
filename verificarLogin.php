<?php
session_start();
require 'baseDatos/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitiza el nombre de usuario y valida entrada
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
    $contrasena = $_POST['contrasena'] ?? '';

    if ($usuario && $contrasena) {
        // Preparar consulta para evitar inyección SQL
        $stmt = $pdo->prepare("SELECT id_usuario, nombre, contrasena, tipo, correo, telefono FROM usuarios WHERE nombre = :usuario LIMIT 1");
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica que el usuario exista y la contraseña coincida
        if ($user && password_verify($contrasena, $user['contrasena'])) {
            // Regenerar sesión para evitar secuestro de sesión
            session_regenerate_id(true);

            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['tipo'] = $user['tipo'];
            $_SESSION['correo'] = $user['correo'];
            $_SESSION['telefono'] = $user['telefono'];

            // Redirecciona con la URL codificada
            header("Location: index.php?nombre_usuario=" . urlencode($usuario));
            exit;
        } else {
            // Mensaje genérico para no revelar si el usuario existe o no
            header("Location: login.php?error=Credenciales incorrectas");
            exit;
        }
    } else {
        header("Location: login.php?error=Faltan campos");
        exit;
    }
} else {
    // Bloquear métodos distintos a POST
    header("HTTP/1.1 405 Method Not Allowed");
    exit;
}
