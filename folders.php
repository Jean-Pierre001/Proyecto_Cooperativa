<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestor de Carpetas</title>
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
    }

    .delete-button {
      position: absolute;
      top: 5px;
      right: 10px;
    }
    .folder-location {
      font-size: 13px;
      color: #7f8c8d;
      margin-top: 5px;
      word-break: break-word;
    }
  </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="content-wrapper">
  <h2>Gestor de Carpetas</h2>

  <!--formulario de filtro-->
    <form method="GET" class="form-inline" style="margin-bottom: 20px;">
      <div class="form-group">
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" style="min-width: 500px;">
      </div>
      <button type="submit" class="btn btn-primary">Buscar</button>
      <a href="folders.php" class="btn btn-default">Limpiar</a>
    </form>

  <p>Carpetas registradas en el sistema:</p>

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

      // Consultar todas las carpetas desde la base de datos
      $buscar = $_GET['buscar'] ?? '';

      if (!empty($buscar)) {
        $sql = "SELECT * FROM folders WHERE name LIKE :buscar";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':buscar' => '%' . $buscar . '%']);
      } else {
        $sql = "SELECT * FROM folders";
        $stmt = $pdo->query($sql);
      }

      $folders = $stmt->fetchAll();


      if (count($folders) === 0) {
        echo "<p>No hay carpetas registradas.</p>";
      }

      foreach ($folders as $folder) {
        $folder_name = $folder['name']; // nombre visible
        $folder_system_name = $folder['folder_system_name']; // nombre físico
        $encoded_name = urlencode($folder_system_name);
        $folder_id = $folder['id'];

        echo '
          <div style="position: relative; display: inline-block; margin: 10px;">
            <a href="detailsfolders.php?folder=' . $encoded_name . '" class="folder-card" style="display: block;">
              <div class="folder-icon">
                <span class="glyphicon glyphicon-folder-open"></span>
              </div>
              <div class="folder-name">' . htmlspecialchars($folder_name) . '</div>
              <div class="folder-location">' . htmlspecialchars($folder['location']) . '</div>
            </a>
            <form method="POST" action="delete_folder.php" onsubmit="return confirm(\'¿Eliminar carpeta ' . addslashes(htmlspecialchars($folder_name)) . '?\');" class="delete-button">
              <input type="hidden" name="folder_id" value="' . $folder_id . '">
              <button type="submit" class="btn btn-danger btn-sm">
                <span class="glyphicon glyphicon-trash"></span>
              </button>
            </form>
          </div>
        ';
      }
    ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>

</body>
</html>
