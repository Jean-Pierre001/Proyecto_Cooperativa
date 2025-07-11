<?php
// Inicio: Lógica de descarga ANTES de cualquier salida o include

$base_dir = realpath('folders');
$folder = isset($_GET['folder']) ? $_GET['folder'] : '';
$folder = trim($folder, '/\\');
$target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder);

// Validación básica
if (!$target_path || strpos($target_path, $base_dir) !== 0) {
    die("Acceso no permitido.");
}

if (isset($_GET['download'])) {
    $file_to_download = basename($_GET['download']);
    $file_path = $target_path . DIRECTORY_SEPARATOR . $file_to_download;

    if (file_exists($file_path) && is_file($file_path)) {
        // Forzar descarga
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
    } else {
        die('Archivo no encontrado.');
    }
}

// --- Fin lógica descarga ---

// Ahora sí incluir archivos que pueden generar salida:
include 'includes/session.php';
include 'includes/header.php';
include 'includes/navbar.php';

// --- ELIMINAR ARCHIVOS Y CARPETAS ---
$msg = '';
$msg_error = '';

if (isset($_GET['delete']) && isset($_GET['type'])) {
    $delete_name = basename($_GET['delete']);
    $type = $_GET['type'];
    $delete_path = $target_path . DIRECTORY_SEPARATOR . $delete_name;

    if (!file_exists($delete_path)) {
        $msg_error = "El archivo o carpeta no existe.";
    } else {
        if ($type === 'file' && is_file($delete_path)) {
            if (unlink($delete_path)) {
                $msg = "Archivo <strong>$delete_name</strong> eliminado correctamente.";
            } else {
                $msg_error = "Error al eliminar archivo.";
            }
        } elseif ($type === 'folder' && is_dir($delete_path)) {
            $files_in_folder = array_diff(scandir($delete_path), array('.', '..'));
            if (count($files_in_folder) > 0) {
                $msg_error = "La carpeta no está vacía, no se puede eliminar.";
            } else {
                if (rmdir($delete_path)) {
                    $msg = "Carpeta <strong>$delete_name</strong> eliminada correctamente.";
                } else {
                    $msg_error = "Error al eliminar carpeta.";
                }
            }
        } else {
            $msg_error = "Tipo o archivo/carpeta inválido.";
        }
    }
}

// --- CREAR NUEVA CARPETA ---
if (isset($_POST['new_folder']) && !empty($_POST['folder_name'])) {
    $new_folder = basename($_POST['folder_name']);
    $new_folder_path = $target_path . DIRECTORY_SEPARATOR . $new_folder;
    if (!file_exists($new_folder_path)) {
        mkdir($new_folder_path, 0755, true);
        $msg = "Carpeta <strong>$new_folder</strong> creada exitosamente.";
    } else {
        $msg_error = "La carpeta <strong>$new_folder</strong> ya existe.";
    }
}

// --- SUBIR ARCHIVO ---
if (isset($_POST['upload']) && isset($_FILES['file'])) {
    $file_name = basename($_FILES['file']['name']);
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_dest = $target_path . DIRECTORY_SEPARATOR . $file_name;

    if (move_uploaded_file($file_tmp, $file_dest)) {
        $msg = "Archivo <strong>$file_name</strong> subido correctamente.";
    } else {
        $msg_error = "Error al subir el archivo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Contenido de Carpeta: <?php echo htmlspecialchars($folder ?: 'Raíz'); ?></title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
    body {
      padding-top: 50px;
      background-color: #fefefe;
    }
    .content-wrapper {
      margin-left: 230px;
      padding: 30px;
    }
    .item-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
    }
    .item-box {
      background: #ffffff;
      width: 160px;
      height: 160px;
      border-radius: 15px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.08);
      text-align: center;
      padding: 20px 10px 50px 10px; /* espacio abajo para botones */
      overflow: hidden;
      position: relative;
      word-break: break-word;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .item-box.folder:hover {
      background: #e6f2ff;
    }
    .item-box.file:hover {
      background: #fff5e6;
    }
    .item-icon {
      font-size: 45px;
      color: #2980b9;
      margin-bottom: 10px;
    }
    .item-name {
      font-size: 15px;
      font-weight: 500;
      color: #2c3e50;
      margin-bottom: 10px;
      overflow-wrap: break-word;
    }
    .item-name a {
      color: inherit;
      text-decoration: none;
    }
    .item-name a:hover {
      text-decoration: underline;
    }
    .action-buttons {
      position: absolute;
      bottom: 10px;
      left: 0;
      right: 0;
      display: flex;
      justify-content: center;
      gap: 10px;
    }
    .action-buttons a {
      font-size: 18px;
      color: #555;
      text-decoration: none;
      padding: 4px 8px;
      border-radius: 4px;
      border: 1px solid transparent;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .action-buttons a:hover {
      border-color: #2980b9;
      color: #2980b9;
    }
    .breadcrumb {
      background: none;
      padding-left: 0;
    }
  </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="content-wrapper">
  <h2>Contenido de Carpeta: <?php echo htmlspecialchars($folder ?: 'Raíz'); ?></h2>

  <ol class="breadcrumb">
    <li><a href="folders.php">Raíz</a></li>
    <?php
    if ($folder) {
      $parts = explode('/', $folder);
      $path_accum = '';
      foreach ($parts as $index => $part) {
        $path_accum .= ($index > 0 ? '/' : '') . $part;
        if ($index == count($parts) - 1) {
          echo '<li class="active">' . htmlspecialchars($part) . '</li>';
        } else {
          echo '<li><a href="detailsfolders.php?folder=' . urlencode($path_accum) . '">' . htmlspecialchars($part) . '</a></li>';
        }
      }
    }
    ?>
  </ol>

  <?php if ($msg): ?>
    <div class="alert alert-success"><?php echo $msg; ?></div>
  <?php endif; ?>

  <?php if ($msg_error): ?>
    <div class="alert alert-danger"><?php echo $msg_error; ?></div>
  <?php endif; ?>

  <form method="post" class="form-inline" style="margin-bottom: 20px;">
    <div class="form-group">
      <label>Crear Carpeta:</label>
      <input type="text" name="folder_name" class="form-control" placeholder="Nombre de carpeta" required>
    </div>
    <button type="submit" name="new_folder" class="btn btn-primary">Crear</button>
  </form>

  <form method="post" enctype="multipart/form-data" class="form-inline" style="margin-bottom: 30px;">
    <div class="form-group">
      <label>Subir Archivo:</label>
      <input type="file" name="file" class="form-control" required>
    </div>
    <button type="submit" name="upload" class="btn btn-success">Subir</button>
  </form>

  <div class="item-grid">
    <?php
    $items = scandir($target_path);
    foreach ($items as $item) {
      if ($item === '.' || $item === '..') continue;

      $full_path = $target_path . DIRECTORY_SEPARATOR . $item;
      $is_dir = is_dir($full_path);
      $type_class = $is_dir ? 'folder' : 'file';

      // Link para carpeta: navegar
      if ($is_dir) {
        $link = 'detailsfolders.php?folder=' . urlencode(($folder ? $folder . '/' : '') . $item);
        $name_link = '<a href="' . $link . '">' . htmlspecialchars($item) . '</a>';
      } else {
        // Link para descarga
        $link = 'detailsfolders.php?folder=' . urlencode($folder) . '&download=' . urlencode($item);
        $name_link = '<a href="' . $link . '">' . htmlspecialchars($item) . '</a>';
      }

      echo '<div class="item-box ' . $type_class . '">';
      echo '<div class="item-icon"><span class="glyphicon ' . ($is_dir ? 'glyphicon-folder-close' : 'glyphicon-file') . '"></span></div>';
      echo '<div class="item-name">' . $name_link . '</div>';

      echo '<div class="action-buttons">';
      echo '<a href="detailsfolders.php?folder=' . urlencode($folder) . '&delete=' . urlencode($item) . '&type=' . ($is_dir ? 'folder' : 'file') . '" onclick="return confirm(\'¿Estás seguro que deseas eliminar ' . htmlspecialchars($item) . '?\');" title="Eliminar">';
      echo '<span class="glyphicon glyphicon-trash"></span>';
      echo '</a>';
      echo '</div>';

      echo '</div>';
    }
    ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
