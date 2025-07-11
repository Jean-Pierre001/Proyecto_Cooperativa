<?php
include 'includes/session.php'; 
include 'includes/header.php'; 
include 'includes/navbar.php';
require_once 'includes/conn.php';

$filterName = $_POST['filterName'] ?? '';

// Consulta con filtro
$sql = "SELECT * FROM members WHERE 1=1 ";
$params = [];

if ($filterName !== '') {
    $sql .= " AND name LIKE :name ";
    $params[':name'] = $filterName . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestor de Miembros</title>
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
    .top-actions {
      margin-bottom: 20px;
      text-align: right;
    }
    .btn .bi {
      vertical-align: middle;
      margin-right: 4px;
    }

    /* Tabla mejorada */
    .table thead {
      background-color: #343a40;
      color: #fff;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .table tbody tr:hover {
      background-color: #f1f7fc;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .table td, .table th {
      vertical-align: middle !important;
    }

    .table td .btn {
      padding: 5px 8px;
      font-size: 14px;
    }

    .text-muted {
      font-style: italic;
      color: #999;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .table-responsive {
        border: 0;
      }
      .table thead {
        display: none;
      }
      .table tbody tr {
        display: block;
        margin-bottom: 15px;
        border-radius: 10px;
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
        background-color: #fff;
        padding: 15px;
      }
      .table tbody tr td {
        display: flex;
        justify-content: space-between;
        padding: 8px 10px;
        border: none;
        border-bottom: 1px solid #ddd;
      }
      .table tbody tr td:last-child {
        border-bottom: 0;
      }
      .table tbody tr td::before {
        content: attr(data-label);
        font-weight: 600;
        text-transform: uppercase;
        color: #555;
      }
    }
  </style>
</head>
<body>

<?php include 'includes/modals/modalMembers.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="content-wrapper">
  <h2>Gestor de Miembros</h2>

  <?php
    if (isset($_SESSION['user_data']) && is_array($_SESSION['user_data'])) {
      $user = $_SESSION['user_data'];
      echo "<div class='alert alert-info'>Bienvenido, <strong>{$user['first_name']} {$user['last_name']}</strong>. Rol: <strong>" . ($user['type'] == 1 ? "Administrador" : "Usuario") . "</strong>.</div>";
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
        placeholder="Filtrar por nombre de miembro"
        value="<?= htmlspecialchars($filterName) ?>"
        style="min-width: 500px;"
      />
    </div>
    <button type="submit" class="btn btn-primary ml-2">Filtrar</button>
  </form>

  <div class="top-actions">
    <button class="btn btn-success" data-toggle="modal" data-target="#modalCreateMember">
      <i class="bi bi-person-plus-fill"></i> Agregar Miembro
    </button>
  </div>

  <?php if (empty($members)): ?>
    <p>No se encontraron miembros.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover table-condensed text-center">
        <thead class="thead-dark">
          <tr>
            <th>Nombre</th>
            <th>DNI</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Dirección</th>
            <th>Fecha de Ingreso</th>
            <th>Estado</th>
            <th>Aportes</th>
            <th>Documento</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($members as $member): ?>
            <tr>
              <td data-label="Nombre"><?= htmlspecialchars($member['name']) ?></td>
              <td data-label="DNI"><?= htmlspecialchars($member['dni']) ?></td>
              <td data-label="Teléfono"><?= htmlspecialchars($member['phone']) ?></td>
              <td data-label="Email"><?= htmlspecialchars($member['email']) ?></td>
              <td data-label="Dirección"><?= htmlspecialchars($member['address']) ?></td>
              <td data-label="Fecha de Ingreso"><?= htmlspecialchars($member['entry_date']) ?></td>
              <td data-label="Estado">
                <?php 
                  switch ($member['status']) {
                    case 'active': echo 'Activo'; break;
                    case 'inactive': echo 'Inactivo'; break;
                    case 'retired': echo 'Jubilado'; break;
                    default: echo htmlspecialchars($member['status']);
                  }
                ?>
              </td>
              <td data-label="Aportes">$<?= number_format($member['contributions'], 2) ?></td>
              <td data-label="Documento">
                <?php if (!empty($member['document'])): ?>
                  <a href="members_back/downloadDocument.php?id=<?= $member['id'] ?>" class="btn btn-default btn-xs" title="Descargar documento" target="_blank">
                    <i class="bi bi-file-earmark-arrow-down-fill"></i>
                  </a>
                <?php else: ?>
                  <span class="text-muted">N/D</span>
                <?php endif; ?>
              </td>
              <td data-label="Acciones">
                <button class="btn btn-warning btn-sm" onclick='openEditModal(<?= json_encode($member) ?>)' title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <form method="POST" action="members_back/deleteMember.php" onsubmit="return confirm('¿Eliminar al miembro <?= htmlspecialchars($member['name']) ?>?');" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $member['id'] ?>" />
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
  function openEditModal(member) {
    document.getElementById('edit_id').value = member.id;
    document.getElementById('edit_name').value = member.name;
    document.getElementById('edit_dni').value = member.dni;
    document.getElementById('edit_phone').value = member.phone;
    document.getElementById('edit_email').value = member.email;
    document.getElementById('edit_address').value = member.address;
    document.getElementById('edit_entry_date').value = member.entry_date;
    document.getElementById('edit_status').value = member.status;
    document.getElementById('edit_contributions').value = member.contributions;
    $('#modalEditMember').modal('show');
  }
</script>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>

</body>
</html>
