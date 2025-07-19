<!-- Modal Crear Socio -->
<div class="modal fade" id="modalCreateMember" tabindex="-1" role="dialog" aria-labelledby="crearMiembroLabel">
  <div class="modal-dialog" role="document">
    <form method="POST" action="members_back/createMember.php" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="crearMiembroLabel">Agregar Socio</h4>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <label>Número de Socio</label>
            <input type="number" id="member_number" name="member_number" class="form-control" required />
            <small id="memberNumberFeedback" class="text-danger" style="display:none;"></small>
          </div>
          <div class="form-group">
            <label>Nombre Completo</label>
            <input type="text" name="name" class="form-control" required />
          </div>
          <div class="form-group">
            <label>CUIL</label>
            <input type="text" name="cuil" id="cuil" class="form-control" required placeholder="00-00000000-0" maxlength="13" />
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" name="phone" class="form-control" />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" />
          </div>
          <div class="form-group">
            <label>Dirección</label>
            <input type="text" name="address" class="form-control" />
          </div>
          <div class="form-group">
            <label>Fecha de Ingreso</label>
            <input type="date" name="entry_date" class="form-control" />
          </div>
          <div class="form-group">
            <label>Fecha de Egreso</label>
            <input type="date" name="exit_date" class="form-control" />
          </div>
          <div class="form-group">
            <label>Estado</label>
            <select name="status" class="form-control">
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          <div class="form-group">
            <label>Obra asignada</label>
            <select name="work_site" class="form-control" required>
              <option value="sin-asignar">Sin Asignar</option>        
              <option value="costanera">Costanera</option>
              <option value="cooperativa">Cooperativa</option>
              <option value="terminal">Terminal</option>
            </select>
          </div>
  
          </div>
          <div class="form-group">
            <label>Documentos (puede subir hasta 5 archivos)</label>
            <input type="file" name="documents[]" multiple class="form-control" accept=".pdf,.doc,.docx,.jpg,.png,.jpeg" />
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Socio -->
<div class="modal fade" id="modalEditMember" tabindex="-1" role="dialog" aria-labelledby="editarMiembroLabel">
  <div class="modal-dialog" role="document">
    <form method="POST" action="members_back/updateMember.php" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit_id" />
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="editarMiembroLabel">Editar Socio</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nombre Completo</label>
            <input type="text" name="name" id="edit_name" class="form-control" required />
          </div>
          <div class="form-group">
            <label>CUIL</label>
            <input type="text" name="cuil" id="edit_cuil" class="form-control" required placeholder="00-00000000-0" maxlength="13" />
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" name="phone" id="edit_phone" class="form-control" />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="edit_email" class="form-control" />
          </div>
          <div class="form-group">
            <label>Dirección</label>
            <input type="text" name="address" id="edit_address" class="form-control" />
          </div>
          <div class="form-group">
            <label>Fecha de Ingreso</label>
            <input type="date" name="entry_date" id="edit_entry_date" class="form-control" />
          </div>
          <div class="form-group">
            <label>Fecha de Egreso</label>
            <input type="date" name="exit_date" id="edit_exit_date" class="form-control" />
          </div>
          <div class="form-group">
            <label>Estado</label>
            <select name="status" id="edit_status" class="form-control">
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </div>
          <div class="form-group">
            <label>Obra asignada</label>
            <select name="work_site" id="edit_work_site" class="form-control" required>
              <option value="sin-asignar">Sin Asignar</option>        
              <option value="costanera">Costanera</option>
              <option value="cooperativa">Cooperativa</option>
              <option value="terminal">Terminal</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Agregar nuevos documentos</label>
            <input type="file" name="documents[]" multiple class="form-control" accept=".pdf,.doc,.docx,.jpg,.png,.jpeg" />
          </div>

          <div class="form-group">
            <label>Documentos actuales</label>
            <div id="documentos_actuales" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
              <p class="text-muted">Cargando documentos...</p>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Actualizar</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal para ver documentos -->
<div class="modal fade" id="modalVerDocumentos" tabindex="-1" role="dialog" aria-labelledby="modalVerDocumentosLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Documentos del Socio: <span id="nombreSocioDoc"></span></h4>
      </div>
      <div class="modal-body">
        <div id="contenedorDocumentos">
          <p class="text-muted">Cargando documentos...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<style>
  #contenedorDocumentos table {
    width: 100%;
    border-collapse: collapse;
  }
  #contenedorDocumentos th, #contenedorDocumentos td {
    padding: 8px 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
  }
  #contenedorDocumentos th {
    background-color: #343a40;
    color: white;
    text-transform: uppercase;
  }
  #contenedorDocumentos td .btn-download {
    font-size: 16px;
    color: #007bff;
    cursor: pointer;
    border: none;
    background: none;
  }
  #contenedorDocumentos td .btn-download:hover {
    color: #0056b3;
  }
</style>

