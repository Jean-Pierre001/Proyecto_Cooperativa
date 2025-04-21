<?php

include 'baseDatos/conexion.php';

// Consulta
$sql = "SELECT id_socio, nombre, apellido, dni, cuit_cuil, fecha_ingreso, fecha_egreso FROM socios";
$stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Socios</title>
    <link rel="stylesheet" href="socios.css"> <!-- Enlace al archivo CSS -->
</head>
<body>

    <div class="container">
        <header>
            <h1>Listado de Socios</h1>
        </header>

        <!-- Botón para agregar un socio -->
        <div class="add-button-container">
            <a href="agregar_socio.php" class="btn btn-agregar">Agregar Socio</a>
        </div>

        <!-- Tabla de socios -->
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>DNI</th>
                        <th>CUIT/CUIL</th>
                        <th>Fecha de Ingreso</th>
                        <th>Fecha de Egreso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch()): ?>
                        <tr>
                            <td><?php echo $row['id_socio']; ?></td>
                            <td><?php echo $row['nombre'] . " " . $row['apellido']; ?></td>
                            <td><?php echo $row['dni']; ?></td>
                            <td><?php echo $row['cuit_cuil']; ?></td>
                            <td><?php echo $row['fecha_ingreso']; ?></td>
                            <td><?php echo $row['fecha_egreso']; ?></td>
                            <td>
                                <a href="ver_socio.php?id=<?php echo $row['id_socio']; ?>" class="btn btn-ver">Ver</a>
                                <a href="editar_socio.php?id=<?php echo $row['id_socio']; ?>" class="btn btn-editar">Editar</a>
                                <a href="eliminar_socio.php?id=<?php echo $row['id_socio']; ?>" class="btn btn-eliminar">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>