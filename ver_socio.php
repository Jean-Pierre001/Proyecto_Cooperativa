<?php
include 'baseDatos/conexion.php';

// Verificamos si se pasó el parámetro 'id'
if (!isset($_GET['id'])) {
    echo "Error: id no especificado.";
    exit;
}

$id = $_GET['id'];

// Consulta preparada para obtener los datos del socio
$sql = "SELECT * FROM socios WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_STR);
$stmt->execute();
$socio = $stmt->fetch();

if (!$socio) {
    echo "Socio no encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Socio</title>
    <link rel="stylesheet" href="socios.css">
</head>
<body>

    <div class="container">
        <h1>Detalle del Socio</h1>

        <form>
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" value="<?php echo htmlspecialchars($socio['nombre']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Apellido:</label>
                <input type="text" value="<?php echo htmlspecialchars($socio['apellido']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>id:</label>
                <input type="text" value="<?php echo htmlspecialchars($socio['id']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>CUIT/CUIL:</label>
                <input type="text" value="<?php echo htmlspecialchars($socio['cuit_cuil']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Fecha de Ingreso:</label>
                <input type="text" value="<?php echo htmlspecialchars($socio['fecha_ingreso']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Fecha de Egreso:</label>
                <input type="text" value="<?php echo htmlspecialchars($socio['fecha_egreso']); ?>" disabled>
            </div>
            <!-- Podés agregar más campos si tu tabla tiene más columnas -->
        </form>

        <div class="add-button-container">
            <a href="index.php" class="btn">Volver</a>
        </div>
    </div>

</body>
</html>
