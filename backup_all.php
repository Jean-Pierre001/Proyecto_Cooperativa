<?php
include 'includes/session.php';
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

require_once 'includes/dropbox_helper.php';

try {
    // 1. Respaldar carpetas personalizadas (carpeta /folders/)
    $foldersBase = __DIR__ . '/folders';
    if (is_dir($foldersBase)) {
        $subfolders = array_filter(scandir($foldersBase), function($item) use ($foldersBase) {
            return $item !== '.' && $item !== '..' && is_dir($foldersBase . '/' . $item);
        });

        foreach ($subfolders as $carpeta) {
            $rutaLocal = $foldersBase . '/' . $carpeta;
            $rutaDropbox = '/respaldo_total/folders/' . $carpeta;
            subirCarpetaADropbox($rutaLocal, $rutaDropbox);
        }
    }

    // 2. Respaldar carpetas dentro de /uploads/
    $uploadsBase = __DIR__ . '/uploads';
    if (is_dir($uploadsBase)) {
        $subfolders = array_filter(scandir($uploadsBase), function($item) use ($uploadsBase) {
            return $item !== '.' && $item !== '..' && is_dir($uploadsBase . '/' . $item);
        });

        foreach ($subfolders as $carpeta) {
            $rutaLocal = $uploadsBase . '/' . $carpeta;
            $rutaDropbox = '/respaldo_total/uploads/' . $carpeta;
            subirCarpetaADropbox($rutaLocal, $rutaDropbox);
        }
    }

    $_SESSION['success'] = "Respaldo TOTAL completado correctamente.";
} catch (Exception $e) {
    $_SESSION['error'] = "Error durante respaldo: " . $e->getMessage();
}

header('Location: folders.php');
exit();
