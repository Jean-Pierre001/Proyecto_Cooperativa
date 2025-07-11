<?php
include 'includes/session.php';
include 'includes/header.php';
include 'includes/navbar.php';
require_once 'includes/conn.php';

$filterName = $_POST['filterName'] ?? '';

$sql = "SELECT * FROM inspectors WHERE 1=1 ";
$params = [];

if ($filterName !== '') {
    $sql .= " AND name LIKE :name ";
    $params[':name'] = $filterName . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inspectors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestor de Inspectores</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      padding-top: 50px;
      background-color: #ecf0f1;
    }
    .content-wrapper {
      margin-left: 230px;
      padding: 20px;
      min-height: 90vh;
    }
    .top-actions {
      margin-bottom: 20px;
      text-align: right;
    }
    .btn .bi {
      vertical-align: middle;
      margin-right: 4px;
    }
  </style>
</head>
<body>

<?php include 'includes/modals/modalsinspectors.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="content-wrapper">
  <h2>Gestor de Inspectores</h2>

  <?php
    if (isset($_SESSION['user_data']) && is_array($_SESSION['user_data'])) {
      $user = $_SESSION['user_data'];
      echo "<div class='alert alert-info'>Hola, <strong>{$user['first_name']} {$user['last_name']}</strong>. Estás logueado como <strong>" . ($user['type'] == 1 ? "Administrador" : "Usuario") . "</strong>.</div>";
    } else {
      echo "<div class='alert alert-warning'>No se pudo cargar la información del usuario.</div>";
    }
  ?>

  <form method="POST" class="form-inline mb-3">
    <div class="form-group">
      <input
        type="text"
        name="filterName"
        class="form-control"
        placeholder="Filtrar por nombre del inspector"
        value="<?= htmlspecialchars($filterName) ?>"
        style="min-width: 500px;"
      />
    </div>
    <button type="submit" class="btn btn-primary ml-2">Filtrar</button>
  </form>

  <div class="top-actions">
    <button class="btn btn-success" data-toggle="modal" data-target="#modalCrearInspector">
      <i class="bi bi-person-plus-fill"></i> Crear Inspector
    </button>
  </div>

  <?php if (empty($inspectors)): ?>
    <p>No se encontraron inspectores.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover table-condensed text-center">
        <thead class="thead-dark">
          <tr>
            <th>Nombre</th>
            <th>Modalidad / Nivel</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($inspectors as $insp): ?>
            <tr>
              <td><?= htmlspecialchars($insp['name']) ?></td>
              <td><?= htmlspecialchars($insp['level_modality']) ?></td>
              <td><?= htmlspecialchars($insp['phone'] ?? '') ?></td>
              <td><?= htmlspecialchars($insp['email'] ?? '') ?></td>
              <td>
                <button class="btn btn-warning btn-sm"
                        onclick='abrirEditarInspector(<?= json_encode($insp) ?>)'
                        title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <form method="POST" action="inspectors_back/deleteInspector.php" onsubmit="return confirm('¿Eliminar inspector <?= htmlspecialchars($insp['name']) ?>?');" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $insp['id'] ?>" />
                  <button type="submit" class="btn btn-danger btn-xs" title="Eliminar">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>

<script>
  function abrirEditarInspector(inspector) {
    document.getElementById('editar_id').value = inspector.id;
    document.getElementById('editar_name').value = inspector.name;
    document.getElementById('editar_level_modality').value = inspector.level_modality;
    document.getElementById('editar_phone').value = inspector.phone;
    document.getElementById('editar_email').value = inspector.email;
    $('#modalEditarInspector').modal('show');
  }
</script>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>

</body>
</html>
