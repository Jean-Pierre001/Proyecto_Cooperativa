<?php
function mostrar_modal_archivo($ruta_relativa, $nombre_archivo, $id_modal, $folder_actual = '') {
    $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
    $ruta_esc = htmlspecialchars($ruta_relativa);
    ?>
    <div class="modal fade" id="<?= $id_modal ?>" tabindex="-1" role="dialog" aria-labelledby="<?= $id_modal ?>Label">
      <div class="modal-dialog modal-lg" role="document" style="max-width: 90vw;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="<?= $id_modal ?>Label">Vista previa de <?= htmlspecialchars($nombre_archivo) ?></h4>
          </div>
          <div class="modal-body text-center" style="min-height: 500px;">
            <?php
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                // Imagenes
                echo '<img src="' . $ruta_esc . '" alt="Imagen" style="max-width:100%; max-height:80vh;" class="img-thumbnail">';
            } elseif ($extension === 'pdf') {
                // PDF
                echo '<iframe src="' . $ruta_esc . '" style="width:100%; height:80vh;" frameborder="0"></iframe>';
            } elseif (in_array($extension, ['txt', 'log', 'csv'])) {
                // Texto plano (se puede mostrar con iframe o con <pre>)
                echo '<iframe src="' . $ruta_esc . '" style="width:100%; height:80vh;" frameborder="0"></iframe>';
            } elseif (in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])) {
                // Archivos de Office: intentar usar Office Web Viewer (requiere URL pública)
                $url_ofice_viewer = 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . ltrim($ruta_relativa, '/'));
                echo '<iframe src="' . $url_ofice_viewer . '" style="width:100%; height:80vh;" frameborder="0"></iframe>';
            } elseif (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                // Video HTML5
                echo '<video controls style="max-width:100%; max-height:80vh;">
                        <source src="' . $ruta_esc . '" type="video/' . $extension . '">
                        Tu navegador no soporta el video.
                      </video>';
            } elseif (in_array($extension, ['mp3', 'wav', 'ogg'])) {
                // Audio HTML5
                echo '<audio controls style="width:100%;">
                        <source src="' . $ruta_esc . '" type="audio/' . $extension . '">
                        Tu navegador no soporta audio.
                      </audio>';
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
