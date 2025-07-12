<?php
include 'includes/session.php';

if (!isset($_SESSION['user']) || $_SESSION['user_data']['type'] != 1) {
    header('location: index.php');
    exit();
}

include 'includes/header.php';
include 'includes/navbar.php';
require_once 'includes/conn.php';

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestor de Usuarios</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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

<?php include 'includes/sidebar.php'; ?>

<div class="content-wrapper">
  <h2>Gestor de Usuarios</h2>

  <?php
    if (isset($_SESSION['user_data']) && is_array($_SESSION['user_data'])) {
      $user = $_SESSION['user_data'];
      echo "<div class='alert alert-info'>Bienvenido, <strong>{$user['first_name']} {$user['last_name']}</strong>. Estás logueado como <strong>" . ($user['type'] == 1 ? "Administrador" : "Usuario") . "</strong>.</div>";
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

  <div class="top-actions">
    <button class="btn btn-success" data-toggle="modal" data-target="#modalCrearUsuario">
      <i class="bi bi-person-plus-fill"></i> Crear Usuario
    </button>
  </div>

  <?php if (empty($users)): ?>
    <p>No se encontraron usuarios.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover text-center">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Dirección</th>
            <th>Contacto</th>
            <th>Fecha de creación</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td data-label="Nombre"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
              <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
              <td data-label="Tipo"><?= $user['type'] == 1 ? 'Administrador' : 'Usuario' ?></td>
              <td data-label="Dirección"><?= htmlspecialchars($user['address']) ?></td>
              <td data-label="Contacto"><?= htmlspecialchars($user['contact_info']) ?></td>
              <td data-label="Fecha"><?= htmlspecialchars($user['created_on']) ?></td>
              <td data-label="Acciones">
                <button class="btn btn-warning btn-sm" onclick='abrirEditarUsuario(<?= json_encode($user) ?>)' title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <form method="POST" action="users_back/deleteUser.php" onsubmit="return confirm('¿Eliminar usuario <?= htmlspecialchars($user['email']) ?>?');" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $user['id'] ?>" />
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

<!-- Modal Crear Usuario -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="POST" action="users_back/createUser.php" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Crear Usuario</h4>
        </div>
        <div class="modal-body">
          <input type="text" name="first_name" class="form-control" placeholder="Nombre" required><br>
          <input type="text" name="last_name" class="form-control" placeholder="Apellido" required><br>
          <input type="email" name="email" class="form-control" placeholder="Correo" required><br>
          <input type="password" name="password" class="form-control" placeholder="Contraseña" required><br>
          <select name="type" class="form-control" required>
            <option value="0">Usuario</option>
            <option value="1">Administrador</option>
          </select><br>
          <input type="text" name="address" class="form-control" placeholder="Dirección"><br>
          <input type="text" name="contact_info" class="form-control" placeholder="Contacto"><br>
          <input type="file" name="photo" class="form-control"><br>
          <input type="date" name="created_on" class="form-control" required><br>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="POST" action="users_back/editUser.php" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Editar Usuario</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <input type="text" name="first_name" id="edit_first_name" class="form-control" required><br>
          <input type="text" name="last_name" id="edit_last_name" class="form-control" required><br>
          <input type="email" name="email" id="edit_email" class="form-control" required><br>
          <input type="password" name="password" class="form-control" placeholder="Nueva contraseña (opcional)"><br>
          <select name="type" id="edit_type" class="form-control" required>
            <option value="0">Usuario</option>
            <option value="1">Administrador</option>
          </select><br>
          <input type="text" name="address" id="edit_address" class="form-control"><br>
          <input type="text" name="contact_info" id="edit_contact_info" class="form-control"><br>
          <input type="file" name="photo" class="form-control"><br>
          <input type="date" name="created_on" id="edit_created_on" class="form-control" required><br>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
function abrirEditarUsuario(usuario) {
  $('#edit_id').val(usuario.id);
  $('#edit_first_name').val(usuario.first_name);
  $('#edit_last_name').val(usuario.last_name);
  $('#edit_email').val(usuario.email);
  $('#edit_type').val(usuario.type);
  $('#edit_address').val(usuario.address);
  $('#edit_contact_info').val(usuario.contact_info);
  $('#edit_created_on').val(usuario.created_on);
  $('#modalEditarUsuario').modal('show');
}
</script>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>

</body>
</html>
