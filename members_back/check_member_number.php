<?php
// Reemplazá con tu archivo de conexión
include '../includes/conn.php';

header('Content-Type: application/json');

if (isset($_GET['member_number'])) {
    $number = intval($_GET['member_number']);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM members WHERE member_number = ?");
    $stmt->execute([$number]);
    $count = $stmt->fetchColumn();

    echo json_encode(['exists' => $count > 0]);
    exit;
}

echo json_encode(['error' => 'Falta parámetro']);
