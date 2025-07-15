<?php 
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

require_once 'includes/conn.php';

// Procesar creación de carpeta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_folder'])) {
    $folder_name = trim($_POST['folder_name']);
    
    if (empty($folder_name)) {
        $_SESSION['error'] = "El nombre de la carpeta no puede estar vacío.";
        header("Location: folders.php");
        exit();
    }
    
    // Generar nombre de sistema
    $folder_system_name = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '_', $folder_name));

    $base_dir = realpath(__DIR__ . '/folders');
    if (!$base_dir) {
        $_SESSION['error'] = "No se encontró la carpeta base.";
        header("Location: folders.php");
        exit();
    }

    $new_folder_path = $base_dir . DIRECTORY_SEPARATOR . $folder_system_name;

    if (file_exists($new_folder_path)) {
        $_SESSION['error'] = "La carpeta ya existe.";
        header("Location: folders.php");
        exit();
    }

    if (!mkdir($new_folder_path, 0755, true)) {
        $_SESSION['error'] = "Error al crear la carpeta.";
        header("Location: folders.php");
        exit();
    }

    $created_on = date('Y-m-d');
    $folder_path = 'folders/' . $folder_system_name;

    try {
        $stmt = $pdo->prepare("INSERT INTO folders (name, folder_path, created_on, folder_system_name) VALUES (:name, :folder_path, :created_on, :folder_system_name)");
        $stmt->execute([
            ':name' => $folder_name,
            ':folder_path' => $folder_path,
            ':created_on' => $created_on,
            ':folder_system_name' => $folder_system_name
        ]);
        $_SESSION['success'] = "Carpeta '$folder_name' creada correctamente.";
    } catch (PDOException $e) {
        rmdir($new_folder_path);
        $_SESSION['error'] = "Error al guardar en base de datos: " . $e->getMessage();
    }

    header("Location: folders.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestor de Carpetas</title>
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
<?php include 'includes/modals/modalRename.php'; ?>

<div class="content-wrapper">
  <h2>Gestor de Carpetas</h2>

  <!-- Crear carpeta -->
  <form method="POST" class="form-inline" style="margin-bottom: 10px;">
    <div class="form-group">
      <input type="text" name="folder_name" class="form-control" placeholder="Nombre nueva carpeta" required style="min-width: 300px;">
    </div>
    <button type="submit" name="new_folder" class="btn btn-success">Crear Carpeta</button>
  </form>

  <!-- Buscar carpeta -->
  <form method="GET" class="form-inline" style="margin-bottom: 20px;">
    <div class="form-group">
      <input type="text" name="buscar" class="form-control" placeholder="Buscar Carpeta por nombre..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" style="min-width: 500px;">
    </div>
    <button type="submit" class="btn btn-primary">Buscar</button>
    <a href="folders.php" class="btn btn-default">Limpiar</a>
  </form>

  <!-- Mensajes -->
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <!-- Lista de carpetas -->
  <h3 style="margin-top: 50px;">Carpetas Personalizadas</h3>
  <div class="folder-grid">
    <?php
      $buscar = $_GET['buscar'] ?? '';
      if (!empty($buscar)) {
        $stmt = $pdo->prepare("SELECT * FROM folders WHERE name LIKE :buscar");
        $stmt->execute([':buscar' => '%' . $buscar . '%']);
      } else {
        $stmt = $pdo->query("SELECT * FROM folders");
      }

      $folders = $stmt->fetchAll();

      if (count($folders) === 0) {
        echo "<p>No hay carpetas registradas.</p>";
      } else {
        foreach ($folders as $folder) {
          $folder_name = $folder['name'];
          $folder_system_name = $folder['folder_system_name'];
          $encoded_name = urlencode($folder_system_name);
          $folder_id = $folder['id'];

          echo '
          <div style="position: relative; display: inline-block; margin: 10px;">
            <a href="detailsfolders.php?folder=' . $encoded_name . '" class="folder-card">
              <div class="folder-icon"><span class="glyphicon glyphicon-folder-open"></span></div>
              <div class="folder-name">' . htmlspecialchars($folder_name) . '</div>
            </a>

            <div class="folder-actions">
              <form method="POST" action="delete_folder.php" onsubmit="return confirm(\'¿Eliminar carpeta ' . addslashes(htmlspecialchars($folder_name)) . '?\');">
                <input type="hidden" name="folder_id" value="' . $folder_id . '">
                <button type="submit" title="Eliminar carpeta">
                  <span class="glyphicon glyphicon-trash"></span>
                </button>
              </form>

              <button class="rename-button" 
                data-folder-id="' . $folder_id . '" 
                data-folder-name="' . htmlspecialchars($folder_name, ENT_QUOTES) . '" 
                title="Renombrar carpeta">
                <span class="glyphicon glyphicon-pencil"></span>
              </button>
            </div>
          </div>
          ';
        }
      }
    ?>
</div>

<!-- Sección uploads -->
<h3 style="margin-top: 50px;">Carpetas de Obras (Contiene los archivos de socios)</h3>

<div class="folder-grid">
  <?php
    $uploadsDir = realpath(__DIR__ . '/uploads') . '/';

    if ($uploadsDir && is_dir($uploadsDir)) {
      $uploadFolders = array_filter(scandir($uploadsDir), function($item) use ($uploadsDir) {
        return $item !== '.' && $item !== '..' && is_dir($uploadsDir . $item);
      });

      if (count($uploadFolders) === 0) {
        echo "<p>No hay carpetas de socios.</p>";
      } else {
        foreach ($uploadFolders as $folderName) {
          $displayName = ucwords(str_replace('_', ' ', $folderName));
          $encodedName = urlencode('uploads/' . $folderName);

          echo '
            <div style="position: relative; display: inline-block; margin: 10px;">
              <a href="detailsfolders.php?folder=' . $encodedName . '" class="folder-card" style="display: block;">
                <div class="folder-icon"><span class="glyphicon glyphicon-folder-open"></span></div>
                <div class="folder-name">' . htmlspecialchars($displayName) . '</div>
                <div class="folder-location">uploads/' . htmlspecialchars($folderName) . '</div>
              </a>
            </div>
          ';
        }
      }
    } else {
      echo "<p>La carpeta uploads no existe o no es accesible.</p>";
    }
  ?>
</div>
<br>
<br>
</div>


<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>

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

  document.querySelectorAll('.rename-button').forEach(button => {
    button.addEventListener('click', () => {
      const folderId = button.getAttribute('data-folder-id');
      const folderName = button.getAttribute('data-folder-name');

      // Setear valores en el modal
      document.getElementById('renameFolderId').value = folderId;
      document.getElementById('newFolderName').value = folderName;

      // Abrir modal (usando Bootstrap)
      $('#renameModal').modal('show');
    });
  });

</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
          sidebar.classList.toggle('sidebar-open');
        }
      });
    }
  });
</script>




</body>
</html>
