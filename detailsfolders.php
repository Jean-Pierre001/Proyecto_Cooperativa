<?php
// --- INICIO ---

if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && isset($_POST['delete']) 
    && isset($_POST['folder']) 
    && isset($_POST['type']) 
    && !isset($_POST['bulk_delete']) 
    && !isset($_POST['bulk_download'])
) {
    $folder = trim($_POST['folder'], '/\\');
    $item_to_delete = basename($_POST['delete']);
    $type = $_POST['type'];

    if (strpos($folder, 'trash/') === 0) {
        $base_dir = realpath(__DIR__ . '/trash');
        $folder_subpath = substr($folder, strlen('trash/'));
        $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
    } else {
        $base_dir = realpath(__DIR__ . '/folders');
        $folder_subpath = $folder;
        $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
    }

    $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR);
    $target_path = rtrim($target_path, DIRECTORY_SEPARATOR);

    if (strncmp($target_path, $base_dir, strlen($base_dir)) !== 0) {
        $msg_error = "Acceso no permitido para eliminar.";
    } else {
        if ($type === 'file') {
            $file_path = $target_path . DIRECTORY_SEPARATOR . $item_to_delete;
            if (is_file($file_path)) {
                if (unlink($file_path)) {
                    $msg = "Archivo eliminado correctamente.";
                } else {
                    $msg_error = "Error al eliminar el archivo <strong>$item_to_delete</strong>.";
                }
            } else {
                $msg_error = "Archivo no encontrado.";
            }
        } elseif ($type === 'folder') {
            $folder_path = $target_path . DIRECTORY_SEPARATOR . $item_to_delete;
            if (is_dir($folder_path)) {
                if (@rmdir($folder_path)) {
                    $msg = "Carpeta eliminada correctamente.";
                } else {
                    $msg_error = "No se pudo eliminar la carpeta (¿está vacía?).";
                }
            } else {
                $msg_error = "Carpeta no encontrada.";
            }
        }
    }
}



$folder = isset($_GET['folder']) ? $_GET['folder'] : '';
$folder = trim($folder, '/\\');

if (strpos($folder, 'trash/') === 0) {
    $base_dir = realpath(__DIR__ . '/trash');
    $folder_subpath = substr($folder, strlen('trash/'));
    $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
} else {
    $base_dir = realpath(__DIR__ . '/folders');
    $folder_subpath = $folder;
    $target_path = realpath($base_dir . DIRECTORY_SEPARATOR . $folder_subpath);
}

if (!$base_dir || !$target_path) die("Acceso no permitido: ruta base o carpeta no encontrada.");

$base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR);
$target_path = rtrim($target_path, DIRECTORY_SEPARATOR);

if (strncmp($target_path, $base_dir, strlen($base_dir)) !== 0) die("Acceso no permitido: ruta fuera de base.");

include 'includes/session.php';
include 'includes/header.php';
include 'includes/navbar.php';
require_once 'includes/conn.php';
include 'modal_archivo.php';

function renombrar_carpeta($target_path, $nombre_viejo, $nombre_nuevo) {
    $nombre_viejo = basename($nombre_viejo);
    $nombre_nuevo = basename($nombre_nuevo);

    $ruta_vieja = $target_path . DIRECTORY_SEPARATOR . $nombre_viejo;
    $ruta_nueva = $target_path . DIRECTORY_SEPARATOR . $nombre_nuevo;

    if (!is_dir($ruta_vieja)) {
        return "La carpeta original no existe.";
    }
    if (file_exists($ruta_nueva)) {
        return "Ya existe una carpeta con el nombre nuevo.";
    }

    if (rename($ruta_vieja, $ruta_nueva)) {
        return true;  // Éxito
    } else {
        return "Error al renombrar la carpeta.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Manejo de eliminación múltiple (bulk delete)
    if (isset($_POST['bulk_delete']) && !empty($_POST['selected_items'])) {
        $errors_delete = [];
        $success_delete = [];

        foreach ($_POST['selected_items'] as $item) {
            // $item tiene formato tipo:path, ejemplo: "file:folder1/archivo.txt" o "folder:folder1/subcarpeta"
            list($type, $rel_path) = explode(':', $item, 2);

            // Normalizar paths y calcular ruta absoluta
            $rel_path = trim($rel_path, '/\\');

            if (strpos($rel_path, 'trash/') === 0) {
                $base_dir_bulk = realpath(__DIR__ . '/trash');
                $folder_subpath_bulk = substr($rel_path, strlen('trash/'));
                $abs_path = realpath($base_dir_bulk . DIRECTORY_SEPARATOR . $folder_subpath_bulk);
            } else {
                $base_dir_bulk = realpath(__DIR__ . '/folders');
                $folder_subpath_bulk = $rel_path;
                $abs_path = realpath($base_dir_bulk . DIRECTORY_SEPARATOR . $folder_subpath_bulk);
            }

            // Validar que abs_path esté dentro del base_dir para seguridad
            if (!$abs_path || strncmp($abs_path, $base_dir_bulk, strlen($base_dir_bulk)) !== 0) {
                $errors_delete[] = "$item: Ruta no permitida";
                continue;
            }

            if ($type === 'file') {
                if (is_file($abs_path) && unlink($abs_path)) {
                    $success_delete[] = $rel_path;
                } else {
                    $errors_delete[] = "$rel_path: No se pudo eliminar archivo.";
                }
            } elseif ($type === 'folder') {
                if (is_dir($abs_path)) {
                    if (@rmdir($abs_path)) {
                        $success_delete[] = $rel_path;
                    } else {
                        $errors_delete[] = "$rel_path: Carpeta no vacía o no se pudo eliminar.";
                    }
                } else {
                    $errors_delete[] = "$rel_path: Carpeta no encontrada.";
                }
            }
        }

        // Preparar mensajes para mostrar luego
        if (count($success_delete) > 0) {
            $msg = "Se eliminaron: <strong>" . implode(", ", $success_delete) . "</strong>.";
        }
        if (count($errors_delete) > 0) {
            $msg_error = "Errores al eliminar: <strong>" . implode(", ", $errors_delete) . "</strong>.";
        }
    }
}



$msg = '';
$msg_error = '';

if (isset($_POST['rename_folder']) && !empty($_POST['old_name']) && !empty($_POST['new_name'])) {
    $resultado = renombrar_carpeta($target_path, $_POST['old_name'], $_POST['new_name']);
    if ($resultado === true) {
        $msg = "Carpeta renombrada correctamente.";
    } else {
        $msg_error = $resultado;
    }
}

if (isset($_POST['new_folder']) && !empty($_POST['folder_name'])) {
    $new_folder = basename($_POST['folder_name']);
    $new_folder_path = $target_path . DIRECTORY_SEPARATOR . $new_folder;
    if (!file_exists($new_folder_path)) {
        if (mkdir($new_folder_path, 0755, true)) $msg = "Carpeta <strong>$new_folder</strong> creada exitosamente.";
        else $msg_error = "Error al crear la carpeta.";
    } else $msg_error = "La carpeta <strong>$new_folder</strong> ya existe.";
}

if (isset($_POST['upload']) && isset($_FILES['file'])) {
    $totalFiles = count($_FILES['file']['name']);
    $uploadErrors = [];
    $uploadSuccesses = [];

    for ($i = 0; $i < $totalFiles; $i++) {
        $file_name = basename($_FILES['file']['name'][$i]);
        $file_tmp = $_FILES['file']['tmp_name'][$i];
        $file_dest = $target_path . DIRECTORY_SEPARATOR . $file_name;
        if (move_uploaded_file($file_tmp, $file_dest)) $uploadSuccesses[] = $file_name;
        else $uploadErrors[] = $file_name;
    }

    if (count($uploadSuccesses) > 0)
        $msg = "Archivos subidos correctamente: <strong>" . implode(", ", $uploadSuccesses) . "</strong>.";
    if (count($uploadErrors) > 0)
        $msg_error = "Error al subir los siguientes archivos: <strong>" . implode(", ", $uploadErrors) . "</strong>.";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Contenido de Carpeta: <?php echo htmlspecialchars($folder ?: 'Raíz'); ?></title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
    body { padding-top: 50px; background-color: #e8f0fe; }
    .content-wrapper { margin-left: 230px; padding: 30px; }
    .folder-grid { display: flex; flex-wrap: wrap; gap: 25px; }
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
      padding-bottom: 60px; /* más espacio para botones */
    }

    .folder-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0,0,0,0.15);
      background: linear-gradient(to bottom, #fff7e6, #ffe0b2);
    }

    .folder-icon { font-size: 55px; color: #f1c40f; margin-bottom: 10px; }
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
      pointer-events: auto; /* para que pueda recibir clicks */
      cursor: default;
      user-select: none;
    }

    .folder-actions form,
    .folder-actions button {
      margin: 0;
      padding: 0;
    }

    .folder-actions button,
    .folder-actions .btn {
      color: #f0ad4e;
      font-size: 18px;
      padding: 0;
      background: none;
      border: none;
      cursor: pointer;
      outline: none;
      transition: color 0.2s ease;
    }

    .folder-actions form button {
      color: #d9534f;
    }

    .folder-actions button:hover,
    .folder-actions form button:hover {
      color: #d47a0a;
    }

    .folder-location { font-size: 13px; color: #7f8c8d; margin-top: 5px; word-break: break-word; }

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
  <?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
  <?php if ($msg_error): ?><div class="alert alert-danger"><?php echo $msg_error; ?></div><?php endif; ?>
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
  <form method="post" action="detailsfolders.php?folder=<?= urlencode($folder) ?>">
  <!-- Aquí va todo el listado de carpetas y archivos con checkboxes -->

  <!-- Botones para eliminar o descargar -->
  <div style="margin-top:20px;">
    <button type="submit" name="bulk_delete" class="btn btn-danger" onclick="return confirm('¿Eliminar todos los seleccionados?');">Eliminar seleccionados</button>
    <button type="submit" name="bulk_download" class="btn btn-primary">Descargar seleccionados</button>
  </div>

  <?php
  $items = scandir($target_path);
  $folders = [];
  $files = [];
  foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $full_path = $target_path . DIRECTORY_SEPARATOR . $item;
    if (is_dir($full_path)) $folders[] = $item;
    else $files[] = $item;
  }
  ?>
 <?php if (count($folders) > 0): ?>
  <h3>Carpetas</h3>
  <div class="folder-grid">
    <?php foreach ($folders as $folder_name): 
    $modal_id = 'renameModal_' . md5($folder_name);
    $folder_url = ($folder ? $folder . '/' : '') . $folder_name;
?>
  <div class="folder-card" style="position: relative;">
     <input type="checkbox" class="select-item" name="selected_items[]" value="folder:<?= htmlspecialchars($folder_url) ?>" style="position:absolute; top:8px; left:8px; z-index:10;">
    <a href="detailsfolders.php?folder=<?= urlencode($folder_url) ?>" style="color: inherit; text-decoration: none; flex-grow: 1;">
      <div class="folder-icon"><span class="glyphicon glyphicon-folder-open"></span></div>
      <div class="folder-name"><?= htmlspecialchars($folder_name) ?></div>
    </a>
    <div class="folder-actions">
      <!-- Formulario para eliminar carpeta -->
      <form method="post" action="detailsfolders.php" onsubmit="return confirm('¿Seguro que deseas eliminar la carpeta <?= addslashes(htmlspecialchars($folder_name)) ?>?');" style="display:inline;">
        <input type="hidden" name="folder" value="<?= htmlspecialchars($folder) ?>">
        <input type="hidden" name="delete" value="<?= htmlspecialchars($folder_name) ?>">
        <input type="hidden" name="type" value="folder">
        <button type="submit" class="btn btn-danger btn-xs" title="Eliminar carpeta">
          <span class="glyphicon glyphicon-trash"></span>
        </button>
      </form>

      <!-- Botón para renombrar carpeta -->
      <button type="button" class="btn btn-warning btn-xs" title="Renombrar carpeta" data-toggle="modal" data-target="#<?= $modal_id ?>">
        <span class="glyphicon glyphicon-pencil"></span>
      </button>
    </div>
  </div>

  <!-- Modal renombrar -->
  <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1" role="dialog" aria-labelledby="<?= $modal_id ?>Label">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="post" action="detailsfolders.php?folder=<?= urlencode($folder) ?>">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="<?= $modal_id ?>Label">Renombrar carpeta: <?= htmlspecialchars($folder_name) ?></h4>
          </div>
          <div class="modal-body">
            <input type="hidden" name="old_name" value="<?= htmlspecialchars($folder_url) ?>">
            <div class="form-group">
              <label for="new_name_<?= $modal_id ?>">Nuevo nombre</label>
              <input type="text" class="form-control" id="new_name_<?= $modal_id ?>" name="new_name" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="rename_folder" class="btn btn-primary">Renombrar</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
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
        if (strpos($folder, 'trash/') === 0) {
          $relative_file_path = $folder . '/' . $file_name;
        } elseif ($folder != '') {
          $relative_file_path = 'folders/' . $folder . '/' . $file_name;
        } else {
          $relative_file_path = 'folders/' . $file_name;
        }

        $modal_id = 'modal_' . md5($file_name);
      ?>
      <div class="folder-card" data-toggle="modal" data-target="#<?= $modal_id ?>">
        <input type="checkbox" class="select-item" name="selected_items[]" value="file:<?= htmlspecialchars(($folder ? $folder . '/' : '') . $file_name) ?>" style="position:absolute; top:8px; left:8px; z-index:10;" onclick="event.stopPropagation();">
        <div class="folder-icon"><span class="glyphicon glyphicon-file"></span></div>
        <div class="folder-name"><?= htmlspecialchars($file_name) ?></div>
      </div>
      <?php mostrar_modal_archivo($relative_file_path, $file_name, $modal_id, $folder); ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const btnDownload = document.querySelector('button[name="bulk_download"]');
  const form = btnDownload.closest('form');

  btnDownload.addEventListener('click', function(e) {
    e.preventDefault();

    // Recoger los archivos seleccionados (solo los tipo "file")
    const selectedCheckboxes = Array.from(document.querySelectorAll('input.select-item[name="selected_items[]"]:checked'));
    const files = selectedCheckboxes
      .map(cb => cb.value)
      .filter(v => v.startsWith('file:'))
      .map(v => v.substring(5)); // quitar prefijo "file:"

    if (files.length === 0) {
      alert('No hay archivos seleccionados para descargar.');
      return;
    }

    const folder = <?= json_encode($folder) ?>;

    // Descargar cada archivo con un retardo para evitar saturar al navegador
    let delay = 0;
    files.forEach(file => {
      setTimeout(() => {
        const a = document.createElement('a');
        a.href = `download_file.php?folder=${encodeURIComponent(folder)}&file=${encodeURIComponent(file)}`;
        a.download = file;
        document.body.appendChild(a);
        a.click();
        a.remove();
      }, delay);
      delay += 1000; // 1 segundo entre descargas para asegurar que el navegador procese bien
    });
  });
});
</script>

</body>
</html>
