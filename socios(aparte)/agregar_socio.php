<?php
include 'baseDatos/conexion.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $cuit_cuil = $_POST['cuit_cuil'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $fecha_egreso = $_POST['fecha_egreso'];

    // Consulta para insertar el nuevo socio
    $sql_insert = "INSERT INTO socios (nombre, apellido, dni, cuit_cuil, fecha_ingreso, fecha_egreso) 
                   VALUES (:nombre, :apellido, :dni, :cuit_cuil, :fecha_ingreso, :fecha_egreso)";
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([
        'nombre' => $nombre,
        'apellido' => $apellido,
        'dni' => $dni,
        'cuit_cuil' => $cuit_cuil,
        'fecha_ingreso' => $fecha_ingreso,
        'fecha_egreso' => $fecha_egreso
    ]);

    // Redirigir a la lista de socios después de agregar
    header("Location: socios.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Socio</title>
    <link rel="stylesheet" href="estilos/agregarSocio.css"> <!-- Enlace al archivo CSS -->
</head>
<body>

    <div class="container">
        <header>
            <h1>Agregar Nuevo Socio</h1>
        </header>

        <!-- Formulario para agregar un nuevo socio -->
        <form action="" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" required>

            <label for="dni">DNI:</label>
            <input type="text" id="dni" name="dni" required>

            <label for="cuit_cuil">CUIT/CUIL:</label>
            <input type="text" id="cuit_cuil" name="cuit_cuil" required>

            <label for="fecha_ingreso">Fecha de Ingreso:</label>
            <input type="date" id="fecha_ingreso" name="fecha_ingreso" required>

            <label for="fecha_egreso">Fecha de Egreso:</label>
            <input type="date" id="fecha_egreso" name="fecha_egreso" required>

            <button type="submit" class="btn btn-agregar">Agregar Socio</button>
        </form>
    </div>

</body>
</html>
