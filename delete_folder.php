<?php
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
require_once 'includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['folder_id'])) {
        $folder_id = $_POST['folder_id'];

        try {
            // Buscar carpeta en folders
            $stmt = $pdo->prepare("SELECT * FROM folders WHERE id = ?");
            $stmt->execute([$folder_id]);
            $folder = $stmt->fetch();

            if (!$folder) {
                $_SESSION['error'] = "Carpeta no encontrada en la base de datos.";
                header('Location: folders.php');
                exit;
            } else {
                // Debug: revisar id
                if (empty($folder['id'])) {
                    $_SESSION['error'] = "Error: el ID de la carpeta está vacío.";
                    header('Location: folders.php');
                    exit;
                }
            }

            $trashFolderPath = 'trash/' . $folder['folder_system_name'];
            $trashFolderSystemName = $folder['folder_system_name'];

            $insert = $pdo->prepare("INSERT INTO trash 
                (original_id, name, cue, folder_path, location, folder_system_name, deleted_on) 
                VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
            $insert->execute([
                $folder['id'],
                $folder['name'],
                $folder['cue'],
                $trashFolderPath,        // Ruta actualizada a trash/
                $folder['location'],
                $trashFolderSystemName   // Nombre físico sigue igual
            ]);

            // Eliminar de folders
            $delete = $pdo->prepare("DELETE FROM folders WHERE id = ?");
            $delete->execute([$folder_id]);

            // Mover carpeta física
            $baseFolders = __DIR__ . 'folders/';
            $baseTrash = __DIR__ . 'trash/';

            $oldPath = $baseFolders . $folder['folder_system_name'];
            $newPath = $baseTrash . $folder['folder_system_name'];

            if (!is_dir($baseTrash)) {
                mkdir($baseTrash, 0755, true);
            }

            // Función recursiva para mover carpeta completa
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

            moveFolder($oldPath, $newPath);

            $_SESSION['success'] = "Carpeta movida correctamente a la papelera.";
            header('Location: folders.php');
            exit;

        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al mover la carpeta: " . $e->getMessage();
            header('Location: folders.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "ID de carpeta no especificado.";
        header('Location: folders.php');
        exit;
    }
} else {
    header('Location: folders.php');
    exit;
}
