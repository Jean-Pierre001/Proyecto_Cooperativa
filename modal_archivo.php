<?php
function mostrar_modal_archivo($ruta_relativa, $nombre_archivo, $id_modal, $folder_actual = '') {
    $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
    ?>
    <div class="modal fade" id="<?= $id_modal ?>" tabindex="-1" role="dialog" aria-labelledby="<?= $id_modal ?>Label">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="<?= $id_modal ?>Label">Vista previa de <?= htmlspecialchars($nombre_archivo) ?></h4>
          </div>
          <div class="modal-body text-center" style="min-height: 500px;">
            <?php
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                echo '<img src="' . htmlspecialchars($ruta_relativa) . '" alt="Imagen" style="max-width:100%; max-height:450px;" class="img-thumbnail">';
            } elseif ($extension === 'pdf') {
                echo '<iframe src="' . htmlspecialchars($ruta_relativa) . '" style="width:100%; height:500px;" frameborder="0"></iframe>';
            } elseif (in_array($extension, ['txt', 'log', 'csv'])) {
                echo '<iframe src="' . htmlspecialchars($ruta_relativa) . '" style="width:100%; height:500px;" frameborder="0"></iframe>';
            } else {
                echo '<p class="text-muted">No se puede previsualizar este tipo de archivo.</p>';
            }
            ?>
          </div>
          <div class="modal-footer">
            <form method="post" action="detailsfolders.php" class="pull-left" onsubmit="return confirm('¿Seguro que deseas eliminar este archivo?');">
              <input type="hidden" name="folder" value="<?= htmlspecialchars($folder_actual) ?>">
              <input type="hidden" name="delete" value="<?= htmlspecialchars($nombre_archivo) ?>">
              <input type="hidden" name="type" value="file">
              <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
            <a href="download.php?folder=<?= urlencode($folder_actual) ?>&file=<?= urlencode($nombre_archivo) ?>" class="btn btn-primary">
              Descargar
            </a>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <?php
}
?>
