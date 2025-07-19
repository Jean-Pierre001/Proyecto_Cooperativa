<?php
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

// Aquí pueden entrar tanto admin como usuarios comunes
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Principal</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  
  <style>
    body {
      padding-top: 50px;
      background-color: #ecf0f1;
    }
    .content-wrapper {
      margin-left: 230px;
      padding: 20px;
    }

  .panel {
    border-radius: 8px;
    transition: all 0.3s ease-in-out;
  }

  .panel:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transform: translateY(-3px);
  }

  .shadow {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  }
  </style>
</head>
<body>
  <?php include 'includes/sidebar.php'; ?>
  <?php include 'news.php'; ?>

  <div class="content-wrapper">
    <h2>Bienvenido al Gestor de la Cooperativa De Trabajo LTDA</h2>
    <p>Seleccioná una opción del menú para comenzar.</p>

    <?php
		if (isset($_SESSION['user_data']) && is_array($_SESSION['user_data'])) {
		$user = $_SESSION['user_data'];
		echo "<div class='alert alert-info'>Bienvenido, <strong>{$user['first_name']} {$user['last_name']}</strong>. Estás logueado como <strong>" . ($user['type'] == 1 ? "Administrador" : "Usuario") . "</strong>.</div>";
		} else {
		echo "<div class='alert alert-warning'>No se pudo cargar la información del usuario.</div>";
		}
	?>
  </div>

<div class="container">
  <h2 class="text-center">Novedades y funcionalidades del sistema</h2>

  <div class="container" style="margin-top: 10px;">
  <h2 class="text-center">Archivos que se pueden previsualizar</h2>
  <p class="text-center text-muted" style="margin-bottom: 30px;">
    Esta plataforma permite la vista previa de varios tipos de archivos directamente en el navegador, sin necesidad de descarga.
  </p>

  <div class="row">
    <!-- Imágenes -->
    <div class="col-sm-6">
      <div class="panel panel-success shadow">
        <div class="panel-heading">
          <h4><span class="glyphicon glyphicon-picture"></span> Imágenes</h4>
        </div>
        <div class="panel-body">
          <strong>Formatos:</strong> JPG, JPEG, PNG, GIF, WEBP
        </div>
      </div>
    </div>

    <!-- Documentos PDF -->
    <div class="col-sm-6">
      <div class="panel panel-danger shadow">
        <div class="panel-heading">
          <h4><span class="glyphicon glyphicon-file"></span> Documentos PDF</h4>
        </div>
        <div class="panel-body">
          <strong>Formato:</strong> PDF
        </div>
      </div>
    </div>

    <!-- Texto plano -->
    <div class="col-sm-6">
      <div class="panel panel-info shadow">
        <div class="panel-heading">
          <h4><span class="glyphicon glyphicon-align-left"></span> Archivos de texto</h4>
        </div>
        <div class="panel-body">
          <strong>Formatos:</strong> TXT, LOG, CSV
        </div>
      </div>
    </div>

    <!-- Archivos Office -->
    <div class="col-sm-6">
      <div class="panel panel-warning shadow">
        <div class="panel-heading">
          <h4><span class="glyphicon glyphicon-briefcase"></span> Documentos de Office</h4>
        </div>
        <div class="panel-body">
          <strong>Formatos:</strong> DOC, DOCX, XLS, XLSX, PPT, PPTX
        </div>
      </div>
    </div>

    <!-- Videos -->
    <div class="col-sm-6">
      <div class="panel panel-primary shadow">
        <div class="panel-heading">
          <h4><span class="glyphicon glyphicon-facetime-video"></span> Videos</h4>
        </div>
        <div class="panel-body">
          <strong>Formatos:</strong> MP4, WEBM, OGG
        </div>
      </div>
    </div>

    <!-- Audio -->
    <div class="col-sm-6">
      <div class="panel panel-default shadow">
        <div class="panel-heading">
          <h4><span class="glyphicon glyphicon-music"></span> Audio</h4>
        </div>
        <div class="panel-body">
          <strong>Formatos:</strong> MP3, WAV, OGG
        </div>
      </div>
    </div>

    <!-- No soportados -->
    <div class="col-sm-12">
      <div class="alert alert-warning text-center" style="margin-top: 20px;">
        <strong>⚠️ Archivos no compatibles:</strong> ZIP, RAR, EXE, HTML, PHP, y otros formatos no reconocidos no se pueden previsualizar.
      </div>
    </div>
  </div>
</div>


  <?php
  // Función para mostrar secciones
  function renderSeccion($titulo, $color, $categoria, $items) {
    echo "<h3 class='text-left' style='color: $color; border-bottom: 2px solid $color; padding-bottom: 5px; margin-top: 40px;'>$titulo</h3>";
    echo "<div class='row'>";
    foreach ($items as $item) {
      echo "
      <div class='col-sm-6'>
        <div class='panel panel-" . strtolower($categoria) . " shadow'>
          <div class='panel-heading'>
            <h4><span class='glyphicon {$item['icono']}'></span> {$item['titulo']} 
              <span class='label label-" . strtolower($categoria) . " pull-right'>" . ucfirst($categoria) . "</span>
            </h4>
          </div>
          <div class='panel-body'>
            {$item['descripcion']}
          </div>
        </div>
      </div>
      ";
    }
    echo "</div>";
  }

  renderSeccion("🆕 NUEVO", "#f0ad4e", "warning", $novedades['nuevo']);
  renderSeccion("📅 RECIENTE", "#5bc0de", "info", $novedades['reciente']);
  renderSeccion("📂 FUNCIONALIDADES ESTABLES", "#5cb85c", "success", $novedades['estable']);
  ?>
</div>


<br>
<br>
<br>




  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>
  
</body>
</html>
