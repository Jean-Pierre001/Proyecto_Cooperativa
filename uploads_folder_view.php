<?php
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

include 'includes/header.php';
include 'includes/navbar.php';

$socio = $_GET['socio'] ?? '';

$uploads_path = __DIR__ . '/uploads/';

if (empty($socio)) {
    die("No se especificó el socio.");
}

$folder_path = $uploads_path . $socio . '/';

if (!is_dir($folder_path)) {
    die("La carpeta del socio no existe.");
}

$files = array_filter(scandir($folder_path), function($file) use ($folder_path) {
    return $file !== '.' && $file !== '..' && is_file($folder_path . $file);
});
?>

<div class="content-wrapper" style="margin-left:230px; padding:20px;">
    <h2>Documentos de <?= htmlspecialchars($socio) ?></h2>

    <?php if (empty($files)): ?>
        <p>No hay archivos en esta carpeta.</p>
    <?php else: ?>
        <ul>
        <?php foreach ($files as $file): ?>
            <li>
                <a href="uploads/<?= urlencode($socio) ?>/<?= urlencode($file) ?>" target="_blank">
                    <?= htmlspecialchars($file) ?>
                </a>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a href="folders.php" class="btn btn-default">Volver</a>
</div>

<?php
include 'includes/footer.php';
include 'includes/scripts.php';
?>
