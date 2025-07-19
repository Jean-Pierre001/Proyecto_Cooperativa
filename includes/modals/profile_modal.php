<!-- Agregar -->
<div class="modal fade" id="profile">
    <div class="modal-dialog">
        <div class="modal-content">
          	<div class="modal-header">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              		<span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><b>Perfil del Administrador</b></h4>
          	</div>
          	<div class="modal-body">
            	<form class="form-horizontal" method="POST" action="profile_update.php?return=<?php echo basename($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">

                    <!-- Correo Electrónico -->
          		    <div class="form-group">
                  	    <label for="email" class="col-sm-3 control-label">Correo</label>
                  	    <div class="col-sm-9">
                    	    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                  	    </div>
                    </div>

                    <!-- Contraseña (nueva) -->
                    <div class="form-group">
                        <label for="password" class="col-sm-3 control-label">Contraseña</label>
                        <div class="col-sm-9"> 
                          <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese nueva contraseña si desea cambiarla">
                        </div>
                    </div>

                    <!-- Nombre -->
                    <div class="form-group">
                  	    <label for="firstname" class="col-sm-3 control-label">Nombre</label>
                  	    <div class="col-sm-9">
                    	    <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($admin['first_name']); ?>" required>
                  	    </div>
                    </div>

                    <!-- Apellido -->
                    <div class="form-group">
                  	    <label for="lastname" class="col-sm-3 control-label">Apellido</label>
                  	    <div class="col-sm-9">
                    	    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($admin['last_name']); ?>" required>
                  	    </div>
                    </div>

                    <!-- Dirección -->
                    <div class="form-group">
                        <label for="address" class="col-sm-3 control-label">Dirección</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($admin['address']); ?></textarea>
                        </div>
                    </div>

                    <!-- Información de Contacto -->
                    <div class="form-group">
                        <label for="contact_info" class="col-sm-3 control-label">Contacto</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="contact_info" name="contact_info" value="<?php echo htmlspecialchars($admin['contact_info']); ?>" required>
                        </div>
                    </div>

                    <!-- Foto -->
                    <div class="form-group">
                        <label for="photo" class="col-sm-3 control-label">Foto:</label>
                        <div class="col-sm-9">
                          <input type="file" id="photo" name="photo">
                          <?php if (!empty($admin['photo'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($admin['photo']); ?>" alt="Foto actual" style="margin-top:10px; max-height:100px;">
                          <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <!-- Contraseña Actual -->
                    <div class="form-group">
                        <label for="curr_password" class="col-sm-3 control-label">Contraseña Actual:</label>
                        <div class="col-sm-9">
                          <input type="password" class="form-control" id="curr_password" name="curr_password" placeholder="Ingrese contraseña actual para guardar los cambios" required>
                        </div>
                    </div>

          	</div>
          	<div class="modal-footer">
            	<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            	<button type="submit" class="btn btn-success btn-flat" name="save"><i class="fa fa-check-square-o"></i> Guardar</button>
            	</form>
          	</div>
        </div>
    </div>
</div>
