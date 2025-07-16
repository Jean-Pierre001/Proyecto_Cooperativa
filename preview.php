<?php
include 'includes/session.php';

$folder = isset($_GET['folder']) ? $_GET['folder'] : '';
$file = isset($_GET['file']) ? $_GET['file'] : '';

$folder = trim($folder, '/\\');
$file = basename($file);

// Definir ruta base
if (strpos($folder, 'uploads/') === 0) {
    $base_dir = realpath(__DIR__ . '/uploads');
    $folder_subpath = substr($folder, strlen('uploads/'));
} elseif (strpos($folder, 'trash/') === 0) {
    $base_dir = realpath(__DIR__ . '/trash');
    $folder_subpath = substr($folder, strlen('trash/'));
} else {
    $base_dir = realpath(__DIR__ . '/folders');
    $folder_subpath = $folder;
}

$target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
$file_path = $target_path . DIRECTORY_SEPARATOR . $file;

if (!file_exists($file_path)) {
    die("Archivo no encontrado");
}

$ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
$mime_type = mime_content_type($file_path);

// Descarga de archivo
if (isset($_GET['download'])) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    flush();
    readfile($file_path);
    exit;
}

// Eliminación de archivo
if (isset($_GET['delete'])) {
    if (unlink($file_path)) {
        // Eliminar de BD si es de uploads
        if (strpos($folder, 'uploads/') === 0) {
            require_once 'includes/conn.php';
            $relativePath = $folder_subpath . '/' . $file;
            $stmt = $pdo->prepare("DELETE FROM member_documents WHERE file_path = ?");
            $stmt->execute([$relativePath]);
        }
        header('Location: detailsfolders.php?folder=' . urlencode($folder));
        exit;
    } else {
        die("No se pudo eliminar el archivo");
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Vista previa: <?php echo htmlspecialchars($file); ?></title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
    body { padding: 30px; background-color: #f2f2f2; }
    .preview-container {
      max-width: 90%;
      margin: 0 auto;
      background: #fff;
      padding: 25px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      border-radius: 8px;
      position: relative;
    }
    .top-right {
      position: absolute;
      top: 20px;
      right: 20px;
    }
    .top-right a {
      margin-left: 10px;
    }
    iframe, img, pre {
      width: 100%;
      min-height: 500px;
      border: none;
    }
  </style>
</head>
<body>
<div class="preview-container">
  <div class="top-right">
    <a href="?folder=<?= urlencode($folder) ?>&file=<?= urlencode($file) ?>&download=1" class="btn btn-primary">Descargar</a>
    <a href="?folder=<?= urlencode($folder) ?>&file=<?= urlencode($file) ?>&delete=1" class="btn btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este archivo?');">Eliminar</a>
  </div>

  <h2>Vista previa de: <?= htmlspecialchars($file) ?></h2>
  <hr>

  <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
    <img src="<?= htmlspecialchars($folder . '/' . $file) ?>" alt="Vista previa" class="img-responsive">
  <?php elseif ($ext === 'pdf'): ?>
    <iframe src="<?= htmlspecialchars($folder . '/' . $file) ?>"></iframe>
  <?php elseif (in_array($ext, ['txt', 'log'])): ?>
    <pre><?php echo htmlspecialchars(file_get_contents($file_path)); ?></pre>
  <?php elseif (in_array($ext, ['doc', 'docx', 'xls', 'xlsx'])): ?>
    <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $folder . '/' . $file) ?>" frameborder="0"></iframe>
  <?php else: ?>
    <p>No se puede mostrar vista previa para este tipo de archivo.</p>
  <?php endif; ?>
</div>
</body>
</html>