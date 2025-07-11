<?php include 'includes/header.php'; ?>

<!-- Modal Crear Escuela -->
<div class="modal fade" id="modalCrearEscuela" tabindex="-1" role="dialog" aria-labelledby="modalCrearEscuelaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="schools_back/createSchool.php" id="formCrearEscuela">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" id="modalCrearEscuelaLabel">Crear Nueva Escuela</h4>
        </div>
        <div class="modal-body">
          <!-- Campos del formulario -->
          <div class="form-group">
            <label for="crear_school_name">Nombre de la Escuela</label>
            <input type="text" name="school_name" id="crear_school_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="crear_cue">CUE</label>
            <input type="text" name="cue" id="crear_cue" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="crear_shift">Turno</label>
            <input type="text" name="shift" id="crear_shift" class="form-control">
          </div>
          <div class="form-group">
            <label for="crear_service">Servicio</label>
            <input type="text" name="service" id="crear_service" class="form-control">
          </div>
          <div class="form-group">
            <label for="crear_shared_building">Edificio Compartido</label>
            <select name="shared_building" id="crear_shared_building" class="form-control">
              <option value="0">No</option>
              <option value="1">Sí</option>
            </select>
          </div>
          <div class="form-group">
            <label for="crear_address">Dirección</label>
            <input type="text" name="address" id="crear_address" class="form-control">
          </div>
          <div class="form-group">
            <label for="crear_locality">Localidad</label>
            <input type="text" name="locality" id="crear_locality" class="form-control">
          </div>
          <div class="form-group">
            <label for="crear_phone">Teléfono</label>
            <input type="text" name="phone" id="crear_phone" class="form-control">
          </div>
          <div class="form-group">
            <label for="crear_email">Correo Electrónico</label>
            <input type="email" name="email" id="crear_email" class="form-control">
          </div>
          <div class="form-group">
            <label for="crear_principal">Director</label>
            <input type="text" name="principal" id="crear_principal" class="form-control">
          </div>
          <div class="form-group">
            <label for="crear_vice_principal">Vicedirector</label>
            <input type="text" name="vice_principal" id="crear_vice_principal" class="form-control">
          </div>
          <div class="form-group">
            <label for="crear_secretary">Secretario</label>
            <input type="text" name="secretary" id="crear_secretary" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary btn-flat">Guardar Escuela</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Editar Escuela -->
<div class="modal fade" id="modalEditarEscuela" tabindex="-1" role="dialog" aria-labelledby="modalEditarEscuelaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="schools_back/editSchool.php" id="formEditarEscuela">
        <input type="hidden" name="id" id="editar_id" />
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" id="modalEditarEscuelaLabel">Editar Escuela</h4>
        </div>
        <div class="modal-body">
          <!-- Campos del formulario (mismos que crear) -->
          <div class="form-group">
            <label for="editar_school_name">Nombre de la Escuela</label>
            <input type="text" name="school_name" id="editar_school_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editar_cue">CUE</label>
            <input type="text" name="cue" id="editar_cue" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editar_shift">Turno</label>
            <input type="text" name="shift" id="editar_shift" class="form-control">
          </div>
          <div class="form-group">
            <label for="editar_service">Servicio</label>
            <input type="text" name="service" id="editar_service" class="form-control">
          </div>
          <div class="form-group">
            <label for="editar_shared_building">Edificio Compartido</label>
            <select name="shared_building" id="editar_shared_building" class="form-control">
              <option value="0">No</option>
              <option value="1">Sí</option>
            </select>
          </div>
          <div class="form-group">
            <label for="editar_address">Dirección</label>
            <input type="text" name="address" id="editar_address" class="form-control">
          </div>
          <div class="form-group">
            <label for="editar_locality">Localidad</label>
            <input type="text" name="locality" id="editar_locality" class="form-control">
          </div>
          <div class="form-group">
            <label for="editar_phone">Teléfono</label>
            <input type="text" name="phone" id="editar_phone" class="form-control">
          </div>
          <div class="form-group">
            <label for="editar_email">Correo Electrónico</label>
            <input type="email" name="email" id="editar_email" class="form-control">
          </div>
          <div class="form-group">
            <label for="editar_principal">Director</label>
            <input type="text" name="principal" id="editar_principal" class="form-control">
          </div>
          <div class="form-group">
            <label for="editar_vice_principal">Vicedirector</label>
            <input type="text" name="vice_principal" id="editar_vice_principal" class="form-control">
          </div>
          <div class="form-group">
            <label for="editar_secretary">Secretario</label>
            <input type="text" name="secretary" id="editar_secretary" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary btn-flat">Actualizar Escuela</button>
        </div>
      </form>
    </div>
  </div>
</div>
