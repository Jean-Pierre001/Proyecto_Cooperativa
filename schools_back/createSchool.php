<?php
include '../includes/session.php';
include '../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos de POST y sanitizar
    $school_name = $_POST['school_name'] ?? '';
    $cue = $_POST['cue'] ?? '';
    $shift = $_POST['shift'] ?? '';
    $service = $_POST['service'] ?? '';
    $shared_building = isset($_POST['shared_building']) ? 1 : 0; // checkbox
    $address = $_POST['address'] ?? '';
    $locality = $_POST['locality'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $principal = $_POST['principal'] ?? '';
    $vice_principal = $_POST['vice_principal'] ?? '';
    $secretary = $_POST['secretary'] ?? '';

    // Validar que school_name no esté vacío (puedes agregar más validaciones)
    if (empty($school_name)) {
        $_SESSION['error'] = "El nombre de la escuela es obligatorio.";
        header("Location: ../schools.php");
        exit;
    }

    try {
        $sql = "INSERT INTO schools 
            (school_name, cue, shift, service, shared_building, address, locality, phone, email, principal, vice_principal, secretary)
            VALUES
            (:school_name, :cue, :shift, :service, :shared_building, :address, :locality, :phone, :email, :principal, :vice_principal, :secretary)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':school_name' => $school_name,
            ':cue' => $cue,
            ':shift' => $shift,
            ':service' => $service,
            ':shared_building' => $shared_building,
            ':address' => $address,
            ':locality' => $locality,
            ':phone' => $phone,
            ':email' => $email,
            ':principal' => $principal,
            ':vice_principal' => $vice_principal,
            ':secretary' => $secretary,
        ]);

        $_SESSION['success'] = "Escuela creada correctamente.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al crear la escuela: " . $e->getMessage();
    }

    header("Location: ../schools.php");
    exit;
} else {
    header("Location: ../schools.php");
    exit;
}
?>
