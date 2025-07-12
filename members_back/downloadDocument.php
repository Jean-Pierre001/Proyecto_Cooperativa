<?php
// members_back/downloadDocument.php

include '../includes/session.php';
require_once '../includes/conn.php';

if (!isset($_GET['file']) || empty($_GET['file'])) {
    http_response_code(400);
    exit('Archivo no especificado.');
}

$filename = basename($_GET['file']); // Sanear el nombre para evitar traversal
$filepath = __DIR__ . '/../uploads/' . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    exit('Archivo no encontrado.');
}

// Forzar descarga
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit();
