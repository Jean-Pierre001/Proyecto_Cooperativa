<?php
$uploadDir = 'Files/';
$carpeta = isset($_GET['nombre']) ? basename($_GET['nombre']) : null;
$rutaCompleta = $uploadDir . $carpeta;

if (!$carpeta || !is_dir($rutaCompleta)) {
    die("Carpeta no válida.");
}

$archivos = glob($rutaCompleta . '/*');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $archivoEliminar = $_POST['eliminar'];
    if (file_exists($archivoEliminar)) {
        unlink($archivoEliminar);
        echo "<script>alert('Archivo eliminado correctamente');</script>";
    } else {
        echo "<script>alert('El archivo no existe');</script>";
    }
}

function iconoArchivo($archivo) {
    $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
    $icono = "imagenes/{$ext}.png";
    return file_exists($icono) ? $icono : "imagenes/default.png";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($carpeta) ?></title>
    <link rel="stylesheet" href="estilos/cooperativa.css">
    <script>
        let archivoSeleccionado = null;

        function seleccionarArchivo(archivo) {
            if (archivoSeleccionado) {
                archivoSeleccionado.classList.remove('selected');
            }
            archivo.classList.add('selected');
            archivoSeleccionado = archivo;
            document.querySelector('.eliminar-btn').style.display = 'inline-block';
        }

        function eliminarArchivo() {
            if (archivoSeleccionado) {
                const archivoEliminar = archivoSeleccionado.querySelector('a').getAttribute('href');
                const formData = new FormData();
                formData.append('eliminar', archivoEliminar);

                fetch('cooperativa.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.text())
                  .then(data => {
                    alert('Archivo eliminado correctamente');
                    location.reload();
                }).catch(error => {
                    alert('Error al eliminar el archivo');
                });
            }
        }
    </script>
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <img src="imagenes/logo.jpg" alt="Logo Cooperativa">
        </a>
        <h1><?= htmlspecialchars($carpeta) ?></h1>
    </header>

    <main>
        <section class="contenedor-archivos">
        <?php foreach ($archivos as $archivo): ?>
            <?php if (is_file($archivo)): ?>
                <div class="archivo" onclick="seleccionarArchivo(this)">
                    <img class="icono" src="<?= iconoArchivo($archivo) ?>" alt="icono">
                    <a href="<?= $archivo ?>" download><?= basename($archivo) ?></a>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        </section>

        <button class="eliminar-btn" onclick="eliminarArchivo()">Eliminar archivo seleccionado</button>

        <section class="subir">
            <h2>Subir archivo a <?= htmlspecialchars($carpeta) ?></h2>
            <form method="POST" enctype="multipart/form-data" action="cooperativa.php">
                <input type="hidden" name="carpeta_destino" value="<?= htmlspecialchars($carpeta) ?>">
                <input type="file" name="archivo" required>
                <button type="submit">Subir</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Cooperativa de Trabajo</p>
    </footer>
</body>
</html>
