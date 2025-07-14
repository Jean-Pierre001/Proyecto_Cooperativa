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

// Incluir conexión PDO (si no está ya incluido arriba)
require_once 'includes/conn.php';

if (isset($_GET['delete']) && isset($_GET['type'])) {
    $delete_name = basename($_GET['delete']);
    $type = $_GET['type'];
    $delete_path = $target_path . DIRECTORY_SEPARATOR . $delete_name;

    if (!file_exists($delete_path)) {
        $msg_error = "El archivo o carpeta no existe.";
    } else {
        if ($type === 'file' && is_file($delete_path)) {
            if (unlink($delete_path)) {
                // Si la carpeta es uploads/, eliminar registro en member_documents
                if (strpos($folder, 'uploads/') === 0) {
                    $relativePath = $folder_subpath . '/' . $delete_name;
                    $stmt = $pdo->prepare("DELETE FROM member_documents WHERE file_path = ?");
                    $stmt->execute([$relativePath]);
                }
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
      background-color: #e8f0fe;
    }
    .content-wrapper {
      margin-left: 230px;
      padding: 30px;
    }
    .folder-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
    }
    .folder-card {
      background: #ffffff; 
      width: 180px; 
      height: 180px;
      border-radius: 15px; 
      box-shadow: 0 8px 16px rgba(0,0,0,0.08);
      transition: all 0.3s ease; 
      text-align: center; 
      padding: 20px 10px;
      cursor: pointer; 
      position: relative; 
      overflow: hidden; 
      text-decoration: none;

      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: start;
      padding-bottom: 60px; /* espacio para botones */
    }

    .folder-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0,0,0,0.15);
      background: linear-gradient(to bottom, #fff7e6, #ffe0b2);
    }

    .folder-icon { 
      font-size: 55px; 
      color: #f1c40f; 
      margin-bottom: 10px; 
    }
    .folder-name { 
      font-size: 16px; 
      font-weight: 600; 
      color: #2c3e50; 
      word-break: break-word;
      margin-bottom: 10px;
      text-align: center;
    }

    .folder-actions {
      position: absolute;
      bottom: 12px;
      left: 50%;
      transform: translateX(-50%);
      background: #f9f9f9;
      padding: 6px 14px;
      border-radius: 12px;
      display: flex;
      justify-content: center;
      gap: 15px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.1);
      pointer-events: auto; /* para clicks */
      cursor: default;
      user-select: none;
    }

    .folder-actions form,
    .folder-actions a {
      margin: 0;
      padding: 0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #f0ad4e;
      font-size: 18px;
      background: none;
      border: none;
      cursor: pointer;
      outline: none;
      text-decoration: none;
      transition: color 0.2s ease;
      width: 28px;
      height: 28px;
      border-radius: 6px;
    }

    .folder-actions form button,
    .folder-actions a.delete-btn {
      color: #d9534f;
    }

    .folder-actions form button:hover,
    .folder-actions a.delete-btn:hover {
      color: #d47a0a;
    }

    .folder-actions form button {
      border: none;
      background: none;
      padding: 0;
      font-size: 18px;
      cursor: pointer;
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
      <input type="text" name="folder_name" class="form-control" placeholder="Nombre de carpeta" required>
    </div>
    <button type="submit" name="new_folder" class="btn btn-success">Crear Carpeta</button>
  </form>

  <form method="post" enctype="multipart/form-data" class="form-inline" style="margin-bottom: 30px;">
    <div class="form-group">
      <input type="file" name="file[]" class="form-control" multiple required>
    </div>
    <button type="submit" name="upload" class="btn btn-primary">Subir Archivo(s)</button>
  </form>

  <?php
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
    <div class="folder-grid">
      <?php foreach ($folders as $folder_name): ?>
        <?php 
          $link = 'detailsfolders.php?folder=' . urlencode(($folder ? $folder . '/' : '') . $folder_name);
        ?>
        <div style="position: relative; display: inline-block; margin: 10px;">
          <a href="<?= $link ?>" class="folder-card">
            <div class="folder-icon"><span class="glyphicon glyphicon-folder-open"></span></div>
            <div class="folder-name"><?= htmlspecialchars($folder_name) ?></div>
          </a>

          <div class="folder-actions">
            <form method="GET" action="detailsfolders.php" onsubmit="return confirm('¿Eliminar carpeta <?= addslashes(htmlspecialchars($folder_name)) ?>?');" style="display:inline;">
              <input type="hidden" name="folder" value="<?= htmlspecialchars($folder) ?>">
              <input type="hidden" name="delete" value="<?= htmlspecialchars($folder_name) ?>">
              <input type="hidden" name="type" value="folder">
              <button type="submit" title="Eliminar carpeta">
                <span class="glyphicon glyphicon-trash"></span>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (count($files) > 0): ?>
    <h3>Archivos</h3>
    <div class="folder-grid">
      <?php foreach ($files as $file_name): ?>
        <?php 
          $link = 'detailsfolders.php?folder=' . urlencode($folder) . '&download=' . urlencode($file_name);
        ?>
        <div style="position: relative; display: inline-block; margin: 10px;">
          <a href="<?= $link ?>" class="folder-card">
            <div class="folder-icon"><span class="glyphicon glyphicon-file"></span></div>
            <div class="folder-name"><?= htmlspecialchars($file_name) ?></div>
          </a>

          <div class="folder-actions">
            <form method="GET" action="detailsfolders.php" onsubmit="return confirm('¿Eliminar archivo <?= addslashes(htmlspecialchars($file_name)) ?>?');" style="display:inline;">
              <input type="hidden" name="folder" value="<?= htmlspecialchars($folder) ?>">
              <input type="hidden" name="delete" value="<?= htmlspecialchars($file_name) ?>">
              <input type="hidden" name="type" value="file">
              <button type="submit" title="Eliminar archivo">
                <span class="glyphicon glyphicon-trash"></span>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>

<script>
  // Evitar hover de carpeta cuando mouse está sobre botones de acciones
  document.querySelectorAll('.folder-actions').forEach(actions => {
    const card = actions.previousElementSibling; // el <a> con .folder-card

    actions.addEventListener('mouseenter', () => {
      if(card) card.style.pointerEvents = 'none';
      actions.style.pointerEvents = 'auto';
    });

    actions.addEventListener('mouseleave', () => {
      if(card) card.style.pointerEvents = 'auto';
    });
  });
</script>

</body>
</html>
