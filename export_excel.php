<?php
require 'includes/conn.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

if (!isset($_POST['selected_ids']) || !is_array($_POST['selected_ids'])) {
    exit("No se seleccionaron socios.");
}

$ids = array_map('intval', $_POST['selected_ids']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$sql = "SELECT id, name, cuil, address, phone, entry_date, exit_date 
        FROM members 
        WHERE id IN ($placeholders)";
$stmt = $pdo->prepare($sql);
$stmt->execute($ids);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Listado de Socios');

// Título
$sheet->mergeCells('A1:I1');
$sheet->setCellValue('A1', 'Listado de Socios Seleccionados');
$sheet->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 16],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
]);

// Nuevos encabezados en orden
$headers = [
    'Número de Socio', 'Nombre y Apellido', 'CUIT', 'Ingreso',
    'Acta 1', 'Renuncia', 'Acta 2', 'Dirección', 'Teléfono'
];
$sheet->fromArray($headers, null, 'A2');
$sheet->getStyle('A2:I2')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '305496']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
]);

// Cargar datos reorganizados
$row = 3;
foreach ($data as $member) {
    $sheet->fromArray([
        $member['id'],               // Número de Socio
        $member['name'],             // Nombre y Apellido
        $member['cuil'],             // CUIT
        $member['entry_date'],       // Ingreso
        '',                          // Acta 1
        $member['exit_date'],        // Renuncia
        '',                          // Acta 2
        $member['address'],          // Dirección
        $member['phone'],            // Teléfono
    ], null, "A$row");

    $fillColor = ($row % 2 == 0) ? 'F2F2F2' : 'FFFFFF';
    $sheet->getStyle("A$row:I$row")->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
    ]);
    $row++;
}   

// Autoajuste de columnas
foreach (range('A', 'I') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Descargar archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Padron_De_Socios.xlsx"');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
