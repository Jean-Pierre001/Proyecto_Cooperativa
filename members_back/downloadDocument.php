<?php
require_once '../includes/conn.php';
session_start();

if (!isset($_GET['id'])) {
    die('No member specified.');
}

$id = intval($_GET['id']);

// Obtener el nombre del archivo document asociado al miembro
$stmt = $pdo->prepare("SELECT document FROM members WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$member = $stmt->fetch();

if (!$member || empty($member['document'])) {
    die('No document found for this member.');
}

$filename = $member['document'];
$filepath = realpath('uploads/' . $filename);

// Verificar que el archivo exista dentro de la carpeta uploads para evitar ataques
$uploadsDir = realpath('../../uploads/');
if (!$filepath || strpos($filepath, $uploadsDir) !== 0 || !file_exists($filepath)) {
    die('File not found.');
}

// Enviar headers para descargar el archivo
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit;
