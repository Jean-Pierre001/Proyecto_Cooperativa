<?php
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

require_once 'includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['folder_id'])) {  // cambiar trash_id por folder_id para que coincida con el form
        $trash_id = $_POST['folder_id'];

        try {
            // Buscar carpeta en la papelera
            $stmt = $pdo->prepare("SELECT * FROM trash WHERE id = ?");
            $stmt->execute([$trash_id]);
            $trash = $stmt->fetch();

            if (!$trash) {
                $_SESSION['error'] = "Carpeta no encontrada en la papelera.";
                header('Location: trash.php');
                exit;
            }

            // Restaurar datos en tabla folders (sin location)
            $insert = $pdo->prepare("INSERT INTO folders 
                (name, folder_path, created_on, folder_system_name) 
                VALUES (?, ?, CURDATE(), ?)");

            $folder_path = 'folders/' . $trash['folder_system_name'];

            $insert->execute([
                $trash['name'],
                $folder_path,
                $trash['folder_system_name']
            ]);

            // Mover carpeta físicamente de trash a folders
            $baseTrash = __DIR__ . '/trash/';
            $baseFolders = __DIR__ . '/folders/';

            $oldPath = $baseTrash . $trash['folder_system_name'];
            $newPath = $baseFolders . $trash['folder_system_name'];

            if (!is_dir($baseFolders)) {
                mkdir($baseFolders, 0755, true);
            }

            // Función recursiva para mover carpeta
            function moveFolder($src, $dst) {
                if (!is_dir($src)) {
                    return false; // la carpeta origen no existe
                }
                if (!is_dir($dst)) {
                    mkdir($dst, 0755, true);
                }
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
                return true;
            }

            if (!moveFolder($oldPath, $newPath)) {
                $_SESSION['error'] = "No se encontró la carpeta física para restaurar.";
                header('Location: trash.php');
                exit;
            }

            // Eliminar de la papelera (BD)
            $delete = $pdo->prepare("DELETE FROM trash WHERE id = ?");
            $delete->execute([$trash_id]);

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
