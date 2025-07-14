<?php
require 'includes/conn.php';
require 'vendor/autoload.php'; // cargá todo con composer autoload

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_POST['selected_ids']) && is_array($_POST['selected_ids'])) {
    $ids = array_map('intval', $_POST['selected_ids']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "SELECT name, cuil, address, phone, entry_date, exit_date FROM members WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->fromArray(['Nombre', 'CUIL', 'Dirección', 'Teléfono', 'Fecha Ingreso', 'Fecha Egreso'], NULL, 'A1');

    $row = 2;
    foreach ($data as $member) {
        $sheet->fromArray([
            $member['name'],
            $member['cuil'],
            $member['address'],
            $member['phone'],
            $member['entry_date'],
            $member['exit_date']
        ], NULL, 'A' . $row++);
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="socios_seleccionados.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit();
} else {
    echo "No se seleccionaron socios.";
}
