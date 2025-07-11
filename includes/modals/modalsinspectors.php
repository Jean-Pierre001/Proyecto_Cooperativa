<?php include 'includes/header.php'; ?>

<!-- Modal: Crear Inspector -->
<div class="modal fade" id="modalCrearInspector">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="inspectors_back/createInspector.php">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><b>Crear Nuevo Inspector</b></h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nombre del Inspector</label>
            <input type="text" name="name" class="form-control" required />
          </div>
          <div class="form-group">
            <label>Modalidad / Nivel</label>
            <input type="text" name="level_modality" class="form-control" required />
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" name="phone" class="form-control" />
          </div>
          <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" name="email" class="form-control" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle"></i> Guardar
          </button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Editar Inspector -->
<div class="modal fade" id="modalEditarInspector">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="inspectors_back/editInspector.php">
        <input type="hidden" name="id" id="editar_id" />
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><b>Editar Inspector</b></h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nombre del Inspector</label>
            <input type="text" name="name" id="editar_name" class="form-control" required />
          </div>
          <div class="form-group">
            <label>Modalidad / Nivel</label>
            <input type="text" name="level_modality" id="editar_level_modality" class="form-control" required />
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" name="phone" id="editar_phone" class="form-control" />
          </div>
          <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" name="email" id="editar_email" class="form-control" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">
            <i class="bi bi-pencil-square"></i> Actualizar
          </button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>
