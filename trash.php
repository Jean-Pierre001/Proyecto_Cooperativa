<?php 
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Papelera</title>
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
      padding: 20px 10px 60px 10px;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      text-decoration: none;

      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: start;
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
    }

    .folder-location {
      font-size: 13px;
      color: #7f8c8d;
      word-break: break-word;
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
    }

    .folder-actions form {
      margin: 0;
    }

    .folder-actions button {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 18px;
      padding: 0;
      outline: none;
      transition: color 0.2s ease;
    }

    .folder-actions button.restore-btn { color: #f0ad4e; }
    .folder-actions button.delete-btn { color: #d9534f; }

    .folder-actions button:hover { color: #d47a0a; }
    .folder-actions .delete-btn:hover { color: #b52b27; }
  </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="content-wrapper">
  <h2>Papelera</h2>

  <!-- Filtro de búsqueda -->
  <form method="GET" class="form-inline" style="margin-bottom: 20px;">
    <div class="form-group">
      <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" style="min-width: 500px;">
    </div>
    <button type="submit" class="btn btn-primary">Buscar</button>
    <a href="trash.php" class="btn btn-default">Limpiar</a>
  </form>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <div class="folder-grid">
    <?php
    require_once 'includes/conn.php';

    function deleteFolder($path) {
      foreach (scandir($path) as $item) {
        if ($item === '.' || $item === '..') continue;
        $item_path = $path . DIRECTORY_SEPARATOR . $item;
        is_dir($item_path) ? deleteFolder($item_path) : unlink($item_path);
      }
      return rmdir($path);
    }

    // Borrar carpetas con más de 24 horas
    $stmt_old = $pdo->query("SELECT * FROM trash WHERE deleted_at <= NOW() - INTERVAL 24 HOUR");
    while ($folder_old = $stmt_old->fetch()) {
      $path = 'trash/' . $folder_old['folder_system_name'];
      if (is_dir($path)) deleteFolder($path);
      $pdo->prepare("DELETE FROM trash WHERE id = ?")->execute([$folder_old['id']]);
    }

    // Mostrar carpetas
    $buscar = $_GET['buscar'] ?? '';
    if (!empty($buscar)) {
      $stmt = $pdo->prepare("SELECT * FROM trash WHERE name LIKE :buscar");
      $stmt->execute([':buscar' => '%' . $buscar . '%']);
    } else {
      $stmt = $pdo->query("SELECT * FROM trash");
    }

    $folders = $stmt->fetchAll();

    if (count($folders) === 0) {
      echo "<p>No hay carpetas en la papelera.</p>";
    } else {
      foreach ($folders as $folder) {
        $folder_name = htmlspecialchars($folder['name']);
        $folder_system_name = $folder['folder_system_name'];
        $encoded_name = urlencode('trash/' . $folder_system_name);
        $folder_id = $folder['id'];

        echo '
        <div style="position: relative; display: inline-block; margin: 10px;">
          <a href="detailsfolders.php?folder=' . $encoded_name . '" class="folder-card">
            <div class="folder-icon"><span class="glyphicon glyphicon-folder-open"></span></div>
            <div class="folder-name">' . $folder_name . '</div>
            <div class="folder-location">trash/' . htmlspecialchars($folder_system_name) . '</div>
          </a>

          <div class="folder-actions">
            <!-- Restaurar -->
            <form method="POST" action="restore_folder.php" onsubmit="return confirm(\'¿Restaurar carpeta ' . addslashes($folder_name) . '?\');">
              <input type="hidden" name="folder_id" value="' . $folder_id . '">
              <button type="submit" class="restore-btn" title="Restaurar">
                <span class="glyphicon glyphicon-repeat"></span>
              </button>
            </form>

            <!-- Eliminar -->
            <form method="POST" action="delete_forever.php" onsubmit="return confirm(\'¿Eliminar permanentemente ' . addslashes($folder_name) . '?\');">
              <input type="hidden" name="folder_id" value="' . $folder_id . '">
              <button type="submit" class="delete-btn" title="Eliminar permanentemente">
                <span class="glyphicon glyphicon-trash"></span>
              </button>
            </form>
          </div>
        </div>';
      }
    }
    ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>

</body>
</html>
