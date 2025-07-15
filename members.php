<?php
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
include 'includes/header.php'; 
include 'includes/navbar.php';
require_once 'includes/conn.php';

$filterName = $_POST['filterName'] ?? '';
$filterStatus = $_POST['filterStatus'] ?? '';

$sql = "SELECT * FROM members WHERE 1=1 ";
$params = [];

if ($filterName !== '') {
    $sql .= " AND name LIKE :name ";
    $params[':name'] = $filterName . '%';
}

if ($filterStatus !== '') {
    $sql .= " AND status = :status ";
    $params[':status'] = $filterStatus;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestor de Socios</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body { padding-top: 50px; background-color: #ecf0f1; }
    .content-wrapper { margin-left: 230px; padding: 20px; min-height: 90vh; }
    .top-actions { margin-bottom: 20px; text-align: right; }
    .btn .bi { vertical-align: middle; margin-right: 4px; }
    .table thead { background-color: #343a40; color: #fff; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
    .table tbody tr:hover { background-color: #f1f7fc; cursor: pointer; transition: background-color 0.3s ease; }
    .table td, .table th { vertical-align: middle !important; }
    .table td .btn { padding: 5px 8px; font-size: 14px; }
    .text-muted { font-style: italic; color: #999; }
    @media (max-width: 768px) {
      .table-responsive { border: 0; }
      .table thead { display: none; }
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
      .table tbody tr td:last-child { border-bottom: 0; }
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
  <h2>Gestor de Socios</h2>

  <?php
    if (isset($_SESSION['user_data']) && is_array($_SESSION['user_data'])) {
      $user = $_SESSION['user_data'];
      echo "<div class='alert alert-info'>Bienvenido, <strong>{$user['first_name']} {$user['last_name']}</strong>. Estás logueado como <strong>" . ($user['type'] == 1 ? "Administrador" : "Usuario") . "</strong>.</div>";
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
      <input type="text" name="filterName" class="form-control" placeholder="Filtrar por nombre de miembro" value="<?= htmlspecialchars($filterName) ?>" style="min-width: 300px;" />
    </div>
    <div class="form-group">
      <select name="filterStatus" class="form-control">
        <option value="">-- Estado --</option>
        <option value="activo" <?= $filterStatus === 'activo' ? 'selected' : '' ?>>Activo</option>
        <option value="inactivo" <?= $filterStatus === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary ml-2">Filtrar</button>
  </form>

  <div class="top-actions">
    <button class="btn btn-success" data-toggle="modal" data-target="#modalCreateMember">
      <i class="bi bi-person-plus-fill"></i> Agregar Socio
    </button>
  </div>

  <?php if (empty($members)): ?>
    <p>No se encontraron socios.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover table-condensed text-center">
        <thead class="thead-dark">
          <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>Nº</th>
            <th>Nº Socio</th>
            <th>Nombre Completo</th>
            <th>CUIL</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Dirección</th>
            <th>Fecha Ingreso</th>
            <th>Fecha Egreso</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php $contador = 1; ?>
          <?php foreach ($members as $member): ?>
            <tr>
              <td><input type="checkbox" class="select-item" value="<?= $member['id'] ?>"></td>
              <td data-label="Nº"><?= $contador++ ?></td>
              <td data-label="Número"><?= htmlspecialchars($member['member_number']) ?></td>
              <td data-label="Nombre"><?= htmlspecialchars($member['name']) ?></td>
              <td data-label="CUIL"><?= htmlspecialchars($member['cuil']) ?></td>
              <td data-label="Teléfono"><?= htmlspecialchars($member['phone']) ?></td>
              <td data-label="Email"><?= htmlspecialchars($member['email']) ?></td>
              <td data-label="Dirección"><?= htmlspecialchars($member['address']) ?></td>
              <td data-label="Ingreso"><?= htmlspecialchars($member['entry_date']) ?></td>
              <td data-label="Egreso"><?= htmlspecialchars($member['exit_date']) ?></td>
              <td data-label="Estado">
                <?= $member['status'] === 'activo' ? 'Activo' : ($member['status'] === 'inactivo' ? 'Inactivo' : ucfirst($member['status'])) ?>
              </td>
              <td data-label="Acciones">
                <button type="button" class="btn btn-warning btn-sm" onclick='openEditModal(<?= json_encode($member) ?>)' title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick='verDocumentos(<?= $member["id"] ?>, <?= json_encode(htmlspecialchars($member["name"])) ?>)' title="Ver documentos">
                  <i class="bi bi-folder2-open"></i>
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

    <div style="margin-top: 15px; text-align: right;">
      <button id="exportSelected" class="btn btn-success">
        <i class="bi bi-file-earmark-excel"></i> Exportar seleccionados a Excel
      </button>
    </div>
    <br>
    <br>
  <?php endif; ?>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
  // Seleccionar/deseleccionar todos
  $('#selectAll').on('change', function() {
    $('.select-item').prop('checked', $(this).prop('checked'));
  });

  // Actualizar selectAll si algún checkbox cambia
  $('.select-item').on('change', function() {
    if ($('.select-item:checked').length === $('.select-item').length) {
      $('#selectAll').prop('checked', true);
    } else {
      $('#selectAll').prop('checked', false);
    }
  });

  // Exportar seleccionados a Excel (envía ids via POST usando formulario dinámico)
  $('#exportSelected').on('click', function() {
    const selected = $('.select-item:checked').map(function() {
      return $(this).val();
    }).get();

    if (selected.length === 0) {
      alert('Por favor seleccioná al menos un socio para exportar.');
      return;
    }

    // Crear formulario dinámico para enviar POST
    const form = $('<form>', {
      action: 'export_excel.php',
      method: 'POST'
    });

    selected.forEach(id => {
      form.append($('<input>', {
        type: 'hidden',
        name: 'selected_ids[]',
        value: id
      }));
    });

    $('body').append(form);
    form.submit();
  });

  // Función para abrir modal de editar socio
  function openEditModal(member) {
    document.getElementById('edit_id').value = member.id;
    document.getElementById('edit_name').value = member.name;
    document.getElementById('edit_cuil').value = member.cuil;
    document.getElementById('edit_phone').value = member.phone;
    document.getElementById('edit_email').value = member.email;
    document.getElementById('edit_address').value = member.address;
    document.getElementById('edit_entry_date').value = member.entry_date;
    document.getElementById('edit_exit_date').value = member.exit_date;
    document.getElementById('edit_status').value = member.status;
    document.getElementById('edit_work_site').value = member.work_site;

    const docsDiv = document.getElementById('documentos_actuales');
    docsDiv.innerHTML = '<p class="text-muted">Cargando documentos...</p>';

    fetch('members_back/getDocuments.php?member_id=' + member.id)
      .then(response => response.json())
      .then(data => {
        if (!Array.isArray(data) || data.length === 0) {
          docsDiv.innerHTML = '<p class="text-muted">Este socio no tiene documentos cargados.</p>';
          return;
        }
        let html = '<ul style="list-style:none; padding-left:0;">';
        data.forEach(doc => {
          html += `
            <li style="margin-bottom: 8px;">
              <a href="uploads/${doc.file_path}" target="_blank">${doc.file_path}</a>
              <label style="margin-left: 10px;">
                <input type="checkbox" name="delete_docs[]" value="${doc.id}"> Eliminar
              </label>
            </li>
          `;
        });
        html += '</ul>';
        docsDiv.innerHTML = html;
      })
      .catch(() => {
        docsDiv.innerHTML = '<p class="text-danger">Error al cargar los documentos.</p>';
      });

    $('#modalEditMember').modal('show');
  }

  // Función para ver documentos del socio
  function verDocumentos(memberId, memberName) {
    $('#nombreSocioDoc').text(memberName);
    $('#contenedorDocumentos').html('<p class="text-muted">Cargando documentos...</p>');
    $('#modalVerDocumentos').modal('show');

    fetch('members_back/getDocuments.php?member_id=' + memberId)
      .then(response => response.json())
      .then(data => {
        if (!Array.isArray(data) || data.length === 0) {
          $('#contenedorDocumentos').html('<p class="text-muted">Este socio no tiene documentos cargados.</p>');
          return;
        }
        let html = '<table class="table table-striped">';
        html += '<thead><tr><th>Nombre del archivo</th><th>Acción</th></tr></thead><tbody>';
        data.forEach(doc => {
          html += `<tr>
            <td>${doc.document_type ? doc.document_type + ': ' : ''}${doc.file_path}</td>
            <td>
              <a href="uploads/${doc.file_path}" download class="btn btn-sm btn-primary">
                <i class="bi bi-download"></i> Descargar
              </a>
            </td>
          </tr>`;
        });
        html += '</tbody></table>';
        $('#contenedorDocumentos').html(html);
      })
      .catch(() => {
        $('#contenedorDocumentos').html('<p class="text-danger">Error al cargar documentos.</p>');
      });
  }
</script>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>

</body>
</html>
