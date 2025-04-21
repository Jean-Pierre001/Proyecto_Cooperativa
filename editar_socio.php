<?php
include 'baseDatos/conexion.php';

if (isset($_GET['id'])) {
    $id_socio = $_GET['id'];

    // Consulta para obtener los datos del socio
    $sql = "SELECT * FROM socios WHERE id_socio = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_socio]);

    $socio = $stmt->fetch();
} else {
    // Redirigir a la lista de socios si no se pasa un ID
    header("Location: socios.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Si se envía el formulario, actualizar los datos
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $cuit_cuil = $_POST['cuit_cuil'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $fecha_egreso = $_POST['fecha_egreso'];

    $sql_update = "UPDATE socios SET nombre = :nombre, apellido = :apellido, dni = :dni, 
                    cuit_cuil = :cuit_cuil, fecha_ingreso = :fecha_ingreso, fecha_egreso = :fecha_egreso 
                    WHERE id_socio = :id";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([
        'nombre' => $nombre,
        'apellido' => $apellido,
        'dni' => $dni,
        'cuit_cuil' => $cuit_cuil,
        'fecha_ingreso' => $fecha_ingreso,
        'fecha_egreso' => $fecha_egreso,
        'id' => $id_socio
    ]);

    // Redirigir a la página de listado de socios después de la actualización
    header("Location: socios.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Socio</title>
    <link rel="stylesheet" href="editar_socio.css"> <!-- Enlace al archivo CSS -->
</head>
<body>

    <div class="container">
        <header>
            <h1>Editar Socio</h1>
        </header>

        <form action="editar_socio.php?id=<?php echo $socio['id_socio']; ?>" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $socio['nombre']; ?>" required>

            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" value="<?php echo $socio['apellido']; ?>" required>

            <label for="dni">DNI:</label>
            <input type="text" id="dni" name="dni" value="<?php echo $socio['dni']; ?>" required>

            <label for="cuit_cuil">CUIT/CUIL:</label>
            <input type="text" id="cuit_cuil" name="cuit_cuil" value="<?php echo $socio['cuit_cuil']; ?>" required>

            <label for="fecha_ingreso">Fecha de Ingreso:</label>
            <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo $socio['fecha_ingreso']; ?>" required>

            <label for="fecha_egreso">Fecha de Egreso:</label>
            <input type="date" id="fecha_egreso" name="fecha_egreso" value="<?php echo $socio['fecha_egreso']; ?>" required>

            <button type="submit" class="btn btn-editar">Actualizar</button>
        </form>
    </div>

</body>
</html>
