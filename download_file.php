<?php
// download_file.php
session_start();

$folder = $_GET['folder'] ?? '';
$file = $_GET['file'] ?? '';

if (!$folder || !$file) {
    http_response_code(400);
    exit('Parámetros inválidos');
}

$folder = trim($folder, '/\\');
$file = basename($file); // para evitar directorios fuera de lugar

if (strpos($folder, 'trash/') === 0) {
    $base_dir = realpath(__DIR__ . '/trash');
    $folder_subpath = substr($folder, strlen('trash/'));
    $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
} else {
    $base_dir = realpath(__DIR__ . '/folders');
    $folder_subpath = $folder;
    $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
}

if (!$base_dir || !$target_path) {
    http_response_code(403);
    exit('Acceso no permitido');
}

$base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR);
$target_path = rtrim($target_path, DIRECTORY_SEPARATOR);

if (strncmp($target_path, $base_dir, strlen($base_dir)) !== 0) {
    http_response_code(403);
    exit('Acceso no permitido');
}

$file_path = $target_path . DIRECTORY_SEPARATOR . $file;

if (!is_file($file_path)) {
    http_response_code(404);
    exit('Archivo no encontrado');
}

// Forzar descarga:
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));
flush();
readfile($file_path);
exit;
