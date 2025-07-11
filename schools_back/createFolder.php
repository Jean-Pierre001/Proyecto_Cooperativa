<?php
include '../includes/session.php';
require_once '../includes/conn.php';

$cue = $_GET['CUE'] ?? null;
$name = $_GET['nombreEscuela'] ?? '';
$location = $_GET['localidad'] ?? '';

if (!$cue) {
    $_SESSION['error'] = "No se recibió el CUE de la escuela.";
    header("Location: ../schools.php");
    exit;
}

function sanitizeFolderName($name) {
    $name = strtolower($name);
    $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
    $name = preg_replace('/[\s-]+/', '_', $name);
    $name = preg_replace('/[^a-z0-9_]/', '', $name);
    $name = trim($name, '_');
    return $name;
}

// Verificar si ya existe carpeta para ese CUE
$sqlCheck = "SELECT * FROM folders WHERE cue = :cue";
$stmtCheck = $pdo->prepare($sqlCheck);
$stmtCheck->execute([':cue' => $cue]);
$folderExists = $stmtCheck->fetch();

if ($folderExists) {
    $_SESSION['error'] = "Ya existe una carpeta para la escuela con CUE $cue.";
    header("Location: ../schools.php");
    exit;
}

// Crear carpeta física
$basePath = __DIR__ . '/../folders/';
$folderSystemName = sanitizeFolderName($name);

// Verificar si ya existe carpeta física con ese nombre exacto
$folderSystemName = sanitizeFolderName($name);
$fullPath = $basePath . $folderSystemName;

if (is_dir($fullPath)) {
    $_SESSION['error'] = "Ya existe una carpeta con el nombre '$folderSystemName'.";
    header("Location: ../schools.php");
    exit;
}

$fullPath = $basePath . $folderSystemName;            // Ruta absoluta
$relativePath = "folders/" . $folderSystemName;       // Ruta para guardar en la BD

if (!mkdir($fullPath, 0755, true)) {
    $_SESSION['error'] = "Error al crear la carpeta física.";
    header("Location: ../schools.php");
    exit;
}

// Insertar registro en la base de datos
$sqlInsert = "INSERT INTO folders (name, cue, folder_path, location, created_on, folder_system_name)
              VALUES (:name, :cue, :folder_path, :location, CURDATE(), :folder_system_name)";
$stmtInsert = $pdo->prepare($sqlInsert);
$stmtInsert->execute([
    ':name' => $name,                          // Nombre original
    ':cue' => $cue,
    ':folder_path' => $relativePath,           // Ruta relativa para mostrar/gestionar
    ':location' => $location,
    ':folder_system_name' => $folderSystemName // Nombre físico real
]);

$_SESSION['success'] = "Carpeta creada exitosamente para la escuela con CUE $cue.";
header("Location: ../schools.php");
exit;
