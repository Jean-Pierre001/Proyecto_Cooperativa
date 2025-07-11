<!-- Modal: Crear Miembro -->
<div class="modal fade" id="modalCreateMember" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <form action="members_back/createMember.php" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header bg-success">
          <h4 class="modal-title">Agregar Miembro</h4>
        </div>
        <div class="modal-body">
          <div class="form-group"><label>Nombre</label><input type="text" name="name" class="form-control" required></div>
          <div class="form-group"><label>DNI</label><input type="text" name="dni" class="form-control" required></div>
          <div class="form-group"><label>Teléfono</label><input type="text" name="phone" class="form-control"></div>
          <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control"></div>
          <div class="form-group"><label>Dirección</label><input type="text" name="address" class="form-control"></div>
          <div class="form-group"><label>Fecha de Ingreso</label><input type="date" name="entry_date" class="form-control"></div>
          <div class="form-group">
            <label>Estado</label>
            <select name="status" class="form-control">
              <option value="active">Activo</option>
              <option value="inactive">Inactivo</option>
              <option value="retired">Jubilado</option>
            </select>
          </div>
          <div class="form-group"><label>Aportes</label><input type="number" step="0.01" name="contributions" class="form-control"></div>
          <div class="form-group"><label>Documento (PDF)</label><input type="file" name="document" class="form-control"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Editar Miembro -->
<div class="modal fade" id="modalEditMember" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <form action="members_back/updateMember.php" method="POST">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h4 class="modal-title">Editar Miembro</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <div class="form-group"><label>Nombre</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
          <div class="form-group"><label>DNI</label><input type="text" name="dni" id="edit_dni" class="form-control" required></div>
          <div class="form-group"><label>Teléfono</label><input type="text" name="phone" id="edit_phone" class="form-control"></div>
          <div class="form-group"><label>Email</label><input type="email" name="email" id="edit_email" class="form-control"></div>
          <div class="form-group"><label>Dirección</label><input type="text" name="address" id="edit_address" class="form-control"></div>
          <div class="form-group"><label>Fecha de Ingreso</label><input type="date" name="entry_date" id="edit_entry_date" class="form-control"></div>
          <div class="form-group">
            <label>Estado</label>
            <select name="status" id="edit_status" class="form-control">
              <option value="active">Activo</option>
              <option value="inactive">Inactivo</option>
              <option value="retired">Jubilado</option>
            </select>
          </div>
          <div class="form-group"><label>Aportes</label><input type="number" step="0.01" name="contributions" id="edit_contributions" class="form-control"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Actualizar</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>
