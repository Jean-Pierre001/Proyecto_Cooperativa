<?php
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

require_once 'includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rename_folder'], $_POST['folder_id'], $_POST['new_name'])) {
    $folder_id = intval($_POST['folder_id']);
    $new_name = trim($_POST['new_name']);

    if (empty($new_name)) {
        $_SESSION['error'] = "El nombre de la carpeta no puede estar vacío.";
        header("Location: folders.php");
        exit();
    }

    // Obtener datos actuales
    $stmt = $pdo->prepare("SELECT * FROM folders WHERE id = :id");
    $stmt->execute([':id' => $folder_id]);
    $folder = $stmt->fetch();

    if (!$folder) {
        $_SESSION['error'] = "Carpeta no encontrada.";
        header("Location: folders.php");
        exit();
    }

    $old_system_name = $folder['folder_system_name'];
    $old_folder_path = realpath(__DIR__ . '/folders/' . $old_system_name);

    if (!$old_folder_path || !is_dir($old_folder_path)) {
        $_SESSION['error'] = "La carpeta física no existe.";
        header("Location: folders.php");
        exit();
    }

    // Generar nuevo nombre sistema
    $new_system_name = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '_', $new_name));
    $new_folder_path = realpath(__DIR__ . '/folders') . DIRECTORY_SEPARATOR . $new_system_name;

    // Verificar si ya existe carpeta con nuevo nombre
    if (file_exists($new_folder_path)) {
        $_SESSION['error'] = "Ya existe una carpeta con ese nombre.";
        header("Location: folders.php");
        exit();
    }

    // Renombrar carpeta en sistema de archivos
    if (!rename($old_folder_path, $new_folder_path)) {
        $_SESSION['error'] = "Error al renombrar la carpeta en el sistema.";
        header("Location: folders.php");
        exit();
    }

    // Actualizar en base de datos
    try {
        $stmt = $pdo->prepare("UPDATE folders SET name = :name, folder_system_name = :folder_system_name, folder_path = :folder_path WHERE id = :id");
        $stmt->execute([
            ':name' => $new_name,
            ':folder_system_name' => $new_system_name,
            ':folder_path' => 'folders/' . $new_system_name,
            ':id' => $folder_id
        ]);

        $_SESSION['success'] = "Carpeta renombrada correctamente.";
    } catch (PDOException $e) {
        // Intentar revertir renombrado
        rename($new_folder_path, $old_folder_path);
        $_SESSION['error'] = "Error al actualizar en base de datos: " . $e->getMessage();
    }

    header("Location: folders.php");
    exit();
} else {
    header("Location: folders.php");
    exit();
}
