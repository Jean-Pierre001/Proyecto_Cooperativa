<?php
require 'includes/session.php';
require 'includes/conn.php';

if (!isset($_POST['folder_id'])) {
    $_SESSION['error'] = 'ID de carpeta no proporcionado.';
    header('Location: trash.php');
    exit();
}

$folder_id = $_POST['folder_id'];

// Obtener nombre del sistema
$stmt = $pdo->prepare("SELECT folder_system_name FROM trash WHERE id = ?");
$stmt->execute([$folder_id]);
$folder = $stmt->fetch();

if (!$folder) {
    $_SESSION['error'] = 'Carpeta no encontrada.';
    header('Location: trash.php');
    exit();
}

$folder_path = 'trash/' . $folder['folder_system_name'];

// Eliminar carpeta físicamente (si existe)
if (is_dir($folder_path)) {
    // Función para eliminar carpeta con todo su contenido
    function deleteFolder($path) {
        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') continue;
            $item_path = $path . DIRECTORY_SEPARATOR . $item;
            is_dir($item_path) ? deleteFolder($item_path) : unlink($item_path);
        }
        return rmdir($path);
    }
    deleteFolder($folder_path);
}

// Eliminar de la base de datos
$stmt = $pdo->prepare("DELETE FROM trash WHERE id = ?");
$stmt->execute([$folder_id]);

$_SESSION['success'] = 'Carpeta eliminada permanentemente.';
header('Location: trash.php');
exit();
