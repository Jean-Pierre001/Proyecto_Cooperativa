<?php
require_once '../includes/conn.php';

$member_id = $_GET['member_id'] ?? null;
if (!$member_id) {
    echo json_encode([]);
    exit();
}

$stmt = $pdo->prepare("SELECT id, file_path FROM member_documents WHERE member_id = :member_id");
$stmt->execute([':member_id' => $member_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($documents);
