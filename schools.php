<?php
include 'includes/session.php'; 
include 'includes/header.php'; 
include 'includes/navbar.php';
require_once 'includes/conn.php';

$filterName = $_POST['filterName'] ?? '';

// Consulta con filtro
$sql = "SELECT * FROM schools WHERE 1=1 ";
$params = [];

if ($filterName !== '') {
    $sql .= " AND school_name LIKE :school_name ";
    $params[':school_name'] = $filterName . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$schools = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestor de Escuelas</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" />
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
    /* Opcional: botón crear arriba alineado a la derecha */
    .top-actions {
      margin-bottom: 20px;
      text-align: right;
    }
    /* Ajustes para iconos en botones (Bootstrap 3 no trae bootstrap-icons, se incluyen desde CDN arriba) */
    .btn .bi {
      vertical-align: middle;
      margin-right: 4px;
    }
  </style>
</head>
<body>

<?php include 'includes/modals/modalschools.php'; ?>

<?php include 'includes/sidebar.php'; ?>

<div class="content-wrapper">
  <h2>Gestor de Escuelas</h2>

  <?php
    if (isset($_SESSION['user_data']) && is_array($_SESSION['user_data'])) {
      $user = $_SESSION['user_data'];
      echo "<div class='alert alert-info'>Hola, <strong>{$user['first_name']} {$user['last_name']}</strong>. Estás logueado como <strong>" . ($user['type'] == 1 ? "Administrador" : "Usuario") . "</strong>.</div>";
    } else {
      echo "<div class='alert alert-warning'>No se pudo cargar la información del usuario.</div>";
    }
  ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <form method="POST" class="form-inline mb-3">
    <div class="form-group">
      <input
        type="text"
        name="filterName"
        class="form-control"
        placeholder="Filtrar por nombre de escuela"
        value="<?= htmlspecialchars($filterName) ?>"
        style="min-width: 500px;"
      />
    </div>
    <button type="submit" class="btn btn-primary ml-2">Filtrar</button>
  </form>

  <div class="top-actions">
    <button class="btn btn-success" data-toggle="modal" data-target="#modalCrearEscuela">
      <i class="bi bi-person-plus-fill"></i> Crear Escuela
    </button>
  </div>

  <?php if (empty($schools)): ?>
    <p>No se encontraron escuelas.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover table-condensed text-center">
        <thead class="thead-dark">
          <tr>
            <th>Nombre Escuela</th>
            <th>CUE</th>
            <th>Turno</th>
            <th>Servicio</th>
            <th>Edificio Compartido</th>
            <th>Dirección</th>
            <th>Localidad</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Director</th>
            <th>Vicedirector</th>
            <th>Secretario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($schools as $school): ?>
            <tr>
              <td><?= htmlspecialchars($school['school_name']) ?></td>
              <td><?= htmlspecialchars($school['cue'] ?? '') ?></td>
              <td><?= htmlspecialchars($school['shift'] ?? '') ?></td>
              <td><?= htmlspecialchars($school['service'] ?? '') ?></td>
              <td><?= isset($school['shared_building']) ? ($school['shared_building'] ? 'Sí' : 'No') : 'No' ?></td>
              <td><?= htmlspecialchars($school['address'] ?? '') ?></td>
              <td><?= htmlspecialchars($school['locality'] ?? '') ?></td>
              <td><?= htmlspecialchars($school['phone'] ?? '') ?></td>
              <td><?= htmlspecialchars($school['email'] ?? '') ?></td>
              <td><?= htmlspecialchars($school['principal'] ?? '') ?></td>
              <td><?= htmlspecialchars($school['vice_principal'] ?? '') ?></td>
              <td><?= htmlspecialchars($school['secretary'] ?? '') ?></td>
              <td>
                <a href="../folders.php?CUE=<?= urlencode($school['cue']) ?>" class="btn btn-info btn-xs" title="Ver carpeta">
                  <i class="bi bi-folder-fill"></i>
                </a>
                <button class="btn btn-warning btn-sm" 
                  onclick='abrirEditarModal(<?= json_encode($school) ?>)' 
                  title="Editar escuela">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <form method="POST" action="schools_back/deleteSchool.php" onsubmit="return confirm('¿Eliminar escuela <?= htmlspecialchars($school['school_name']) ?>?');" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $school['id'] ?>" />
                  <button type="submit" class="btn btn-danger btn-xs" title="Eliminar escuela">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                </form>
                <a href="schools_back/createFolder.php?CUE=<?= urlencode($school['cue']) ?>&nombreEscuela=<?= urlencode($school['school_name']) ?>&localidad=<?= urlencode($school['locality']) ?>" class="btn btn-default btn-xs" title="Crear carpeta">
                  <i class="bi bi-folder-plus"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>

<script>
  // Función para abrir modal editar y cargar datos
  function abrirEditarModal(school) {
    // school es un objeto con los datos de la escuela

    document.getElementById('editar_id').value = school.id;
    document.getElementById('editar_school_name').value = school.school_name;
    document.getElementById('editar_cue').value = school.cue;
    document.getElementById('editar_shift').value = school.shift;
    document.getElementById('editar_service').value = school.service;
    document.getElementById('editar_shared_building').value = school.shared_building;
    document.getElementById('editar_address').value = school.address;
    document.getElementById('editar_locality').value = school.locality;
    document.getElementById('editar_phone').value = school.phone;
    document.getElementById('editar_email').value = school.email;
    document.getElementById('editar_principal').value = school.principal;
    document.getElementById('editar_vice_principal').value = school.vice_principal;
    document.getElementById('editar_secretary').value = school.secretary;

    $('#modalEditarEscuela').modal('show');
  }
</script>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>

</body>
</html>
