<?php
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
require_once 'includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['folder_id'])) {
        $folder_id = intval($_POST['folder_id']);

        try {
            // 1. Obtener datos de la carpeta en trash
            $stmt = $pdo->prepare("SELECT * FROM trash WHERE id = ?");
            $stmt->execute([$folder_id]);
            $folder = $stmt->fetch();

            if (!$folder) {
                $_SESSION['error'] = "Carpeta no encontrada en la papelera.";
                header('Location: trash.php');
                exit;
            }

            // 2. Construir nueva ruta física y ruta para BD (carpeta restaurada)
            $folder_system_name = $folder['folder_system_name'];
            $new_folder_path = "folders/" . $folder_system_name;

            // 3. Insertar carpeta en folders actualizando folder_path y folder_system_name
            $insert = $pdo->prepare("INSERT INTO folders (name, cue, folder_path, location, created_on, folder_system_name) VALUES (?, ?, ?, ?, CURDATE(), ?)");
            $insert->execute([
                $folder['name'],
                $folder['cue'],
                $new_folder_path,
                $folder['location'],
                $folder_system_name
            ]);
            $new_folder_id = $pdo->lastInsertId();

            // 4. Borrar carpeta de trash
            $delete = $pdo->prepare("DELETE FROM trash WHERE id = ?");
            $delete->execute([$folder_id]);

            // 5. Mover carpeta física de trash/ a folders/
            $old_path = __DIR__ . '/../trash/' . $folder_system_name;
            $new_path = __DIR__ . '/../folders/' . $folder_system_name;

            if (is_dir($old_path)) {
                if (!is_dir(__DIR__ . '/../folders')) {
                    mkdir(__DIR__ . '/../folders', 0755, true);
                }

                // Función para mover carpeta completa recursivamente
                function moveFolder($src, $dst) {
                    mkdir($dst, 0755, true);
                    foreach (scandir($src) as $file) {
                        if ($file === '.' || $file === '..') continue;

                        $srcFile = $src . DIRECTORY_SEPARATOR . $file;
                        $dstFile = $dst . DIRECTORY_SEPARATOR . $file;

                        if (is_dir($srcFile)) {
                            moveFolder($srcFile, $dstFile);
                        } else {
                            rename($srcFile, $dstFile);
                        }
                    }
                    rmdir($src);
                }

                moveFolder($old_path, $new_path);
            } 

            $_SESSION['success'] = "Carpeta restaurada correctamente.";
            header('Location: trash.php');
            exit;

        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al restaurar la carpeta: " . $e->getMessage();
            header('Location: trash.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "ID de carpeta no especificado.";
        header('Location: trash.php');
        exit;
    }
} else {
    header('Location: trash.php');
    exit;
}
