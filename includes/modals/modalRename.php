<!-- Modal Renombrar Carpeta -->
<div id="renameModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="renameModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="rename_folder.php" id="renameForm" class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="renameModalLabel">Renombrar Carpeta</h4>    
      </div>
      <div class="modal-body">
          <input type="hidden" name="folder_id" id="renameFolderId">
          <div class="form-group">
            <label for="newFolderName">Nuevo nombre</label>
            <input type="text" name="new_name" id="newFolderName" class="form-control" required minlength="1" maxlength="100" autofocus>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="submit" name="rename_folder" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>
