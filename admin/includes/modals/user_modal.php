<!-- Modal Crear Usuario -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1" role="dialog" aria-labelledby="modalCrearUsuarioLabel">
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
    <form method="POST" action="users_back/updateUser.php" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Editar Usuario</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <input type="text" name="first_name" id="edit_first_name" class="form-control" placeholder="Nombre" required><br>
          <input type="text" name="last_name" id="edit_last_name" class="form-control" placeholder="Apellido" required><br>
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
