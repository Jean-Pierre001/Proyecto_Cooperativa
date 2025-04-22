<?php
$uploadDir = 'Files/';

if (isset($_POST['crear_carpeta'])) {
    $nombreCarpeta = $_POST['nombre_carpeta'];
    $carpetaRuta = $uploadDir . $nombreCarpeta;

    if (!file_exists($carpetaRuta)) {
        mkdir($carpetaRuta, 0777, true);
    }
}

if (isset($_FILES['archivo'])) {
    $carpetaSeleccionada = $_POST['carpeta_destino'];
    $archivoTemp = $_FILES['archivo']['tmp_name'];
    $archivoNombre = $_FILES['archivo']['name'];
    $carpetaDestino = $uploadDir . $carpetaSeleccionada;

    if (file_exists($carpetaDestino)) {
        $archivoNombreSaneado = str_replace(" ", "_", $archivoNombre);
        $archivoDestino = $carpetaDestino . '/' . $archivoNombreSaneado;
        move_uploaded_file($archivoTemp, $archivoDestino);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Archivos</title>
    <link rel="stylesheet" href="estilos/cooperativa.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <img src="imagenes/logo.jpg" alt="Logo Cooperativa">
        </a>
        <nav>
            <ul>
                <li><a href="#crear-carpeta">Crear Carpeta</a></li>
                <li><a href="#carpetas">Ver Carpetas</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="crear-carpeta">
            <h2>Crear Carpeta</h2>
            <form method="POST">
                <input type="text" name="nombre_carpeta" placeholder="Nombre de la carpeta" required>
                <button type="submit" name="crear_carpeta">Crear</button>
            </form>
        </section>

        <section id="carpetas">
            <h2>Carpetas Disponibles</h2>
            <div class="contenedor-carpetas">
            <?php
                $carpetas = array_filter(glob($uploadDir . '*'), 'is_dir');
                foreach ($carpetas as $carpeta) {
                    $nombre = basename($carpeta);
                    echo "
                    <div class='carpeta'>
                        <a href='carpeta.php?nombre=$nombre'>
                            <img src='imagenes/carpeta.png' alt='Carpeta'>
                            <span>$nombre</span>
                        </a>
                    </div>";
                }
            ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Cooperativa de Trabajo</p>
    </footer>
</body>
</html>
