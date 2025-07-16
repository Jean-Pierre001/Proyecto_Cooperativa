<?php
// download.php
$folder = isset($_GET['folder']) ? trim($_GET['folder'], '/\\') : '';
$file = isset($_GET['file']) ? basename($_GET['file']) : '';

if (!$file) {
    die("Archivo no especificado.");
}

if (strpos($folder, 'uploads/') === 0) {
    $base_dir = realpath(__DIR__ . '/uploads');
    $folder_subpath = substr($folder, strlen('uploads/'));
    $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
} elseif (strpos($folder, 'trash/') === 0) {
    $base_dir = realpath(__DIR__ . '/trash');
    $folder_subpath = substr($folder, strlen('trash/'));
    $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
} else {
    $base_dir = realpath(__DIR__ . '/folders');
    $folder_subpath = $folder;
    $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
}

$base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR);
$target_path = rtrim($target_path, DIRECTORY_SEPARATOR);

if (strncmp($target_path, $base_dir, strlen($base_dir)) !== 0) {
    die("Acceso no permitido.");
}

$file_path = $target_path . DIRECTORY_SEPARATOR . $file;

if (!is_file($file_path)) {
    die("Archivo no encontrado.");
}

// Headers para descarga
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));
flush();
readfile($file_path);
exit;
