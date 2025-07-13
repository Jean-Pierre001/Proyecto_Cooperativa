<?php
// Inicio: Lógica de descarga ANTES de cualquier salida o include

$folder = isset($_GET['folder']) ? $_GET['folder'] : '';
$folder = trim($folder, '/\\');

// Determinar base_dir y target_path según prefijo
if (strpos($folder, 'uploads/') === 0) {
    $base_dir = realpath(__DIR__ . '/uploads');
    $folder_subpath = substr($folder, strlen('uploads/'));
    $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
} elseif (strpos($folder, 'trash/') === 0) {
    $base_dir = realpath(__DIR__ . '/trash');
    $folder_subpath = substr($folder, strlen('trash/'));
    $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
} else {
    // Carpeta normal en 'folders'
    $base_dir = realpath(__DIR__ . '/folders');
    $folder_subpath = $folder;
    $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
}

// Verificar rutas válidas
if (!$base_dir || !$target_path) {
    die("Acceso no permitido: ruta base o carpeta no encontrada.");
}

// Normalizar sin barra final
$base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR);
$target_path = rtrim($target_path, DIRECTORY_SEPARATOR);

// Comprobar que $target_path está dentro de $base_dir para seguridad
if (strncmp($target_path, $base_dir, strlen($base_dir)) !== 0) {
    die("Acceso no permitido: ruta fuera de base.");
}

// Descarga de archivo
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
        if (mkdir($new_folder_path, 0755, true)) {
            $msg = "Carpeta <strong>$new_folder</strong> creada exitosamente.";
        } else {
            $msg_error = "Error al crear la carpeta.";
        }
    } else {
        $msg_error = "La carpeta <strong>$new_folder</strong> ya existe.";
    }
}

// --- SUBIR ARCHIVO ---
if (isset($_POST['upload']) && isset($_FILES['file'])) {
    $totalFiles = count($_FILES['file']['name']);
    $uploadErrors = [];
    $uploadSuccesses = [];

    for ($i = 0; $i < $totalFiles; $i++) {
        $file_name = basename($_FILES['file']['name'][$i]);
        $file_tmp = $_FILES['file']['tmp_name'][$i];
        $file_dest = $target_path . DIRECTORY_SEPARATOR . $file_name;

        if (move_uploaded_file($file_tmp, $file_dest)) {
            $uploadSuccesses[] = $file_name;
        } else {
            $uploadErrors[] = $file_name;
        }
    }

    if (count($uploadSuccesses) > 0) {
        $msg = "Archivos subidos correctamente: <strong>" . implode(", ", $uploadSuccesses) . "</strong>.";
    }

    if (count($uploadErrors) > 0) {
        $msg_error = "Error al subir los siguientes archivos: <strong>" . implode(", ", $uploadErrors) . "</strong>.";
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
      padding: 20px 10px 50px 10px;
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
    /* Botón eliminar simplificado */
    .action-buttons a {
      background-color: #e74c3c;
      border: none;
      color: white;
      width: 36px;
      height: 36px;
      font-size: 18px;
      border-radius: 50%;
      box-shadow: 0 2px 8px rgba(0,0,0,0.12);
      transition: background-color 0.3s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      padding: 0;
    }
    .action-buttons a:hover {
      background-color: #c0392b;
      box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    }
    .action-buttons a .glyphicon {
      margin: 0;
      font-size: 18px;
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
    <label>Subir Archivo(s):</label>
    <input type="file" name="file[]" class="form-control" multiple required>
  </div>
  <button type="submit" name="upload" class="btn btn-success">Subir</button>
  </form>


  <?php
  // Listar carpetas y archivos por separado
  $items = scandir($target_path);
  $folders = [];
  $files = [];

  foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $full_path = $target_path . DIRECTORY_SEPARATOR . $item;
    if (is_dir($full_path)) {
      $folders[] = $item;
    } else {
      $files[] = $item;
    }
  }
  ?>

  <?php if (count($folders) > 0): ?>
    <h3>Carpetas</h3>
    <div class="item-grid">
      <?php foreach ($folders as $folder_name): ?>
        <?php 
          $link = 'detailsfolders.php?folder=' . urlencode(($folder ? $folder . '/' : '') . $folder_name);
        ?>
        <div class="item-box folder">
          <div class="item-icon"><span class="glyphicon glyphicon-folder-close"></span></div>
          <div class="item-name"><a href="<?php echo $link; ?>"><?php echo htmlspecialchars($folder_name); ?></a></div>
          <div class="action-buttons">
            <a href="detailsfolders.php?folder=<?php echo urlencode($folder); ?>&delete=<?php echo urlencode($folder_name); ?>&type=folder" onclick="return confirm('¿Estás seguro que deseas eliminar <?php echo htmlspecialchars($folder_name); ?>?');" title="Eliminar">
              <span class="glyphicon glyphicon-trash"></span>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (count($files) > 0): ?>
    <h3>Archivos</h3>
    <div class="item-grid">
      <?php foreach ($files as $file_name): ?>
        <?php 
          $link = 'detailsfolders.php?folder=' . urlencode($folder) . '&download=' . urlencode($file_name);
        ?>
        <div class="item-box file">
          <div class="item-icon"><span class="glyphicon glyphicon-file"></span></div>
          <div class="item-name"><a href="<?php echo $link; ?>"><?php echo htmlspecialchars($file_name); ?></a></div>
          <div class="action-buttons">
            <a href="detailsfolders.php?folder=<?php echo urlencode($folder); ?>&delete=<?php echo urlencode($file_name); ?>&type=file" onclick="return confirm('¿Estás seguro que deseas eliminar <?php echo htmlspecialchars($file_name); ?>?');" title="Eliminar">
              <span class="glyphicon glyphicon-trash"></span>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
