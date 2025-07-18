<?php
include 'includes/session.php';

if (!isset($_SESSION['user']) || $_SESSION['user_data']['type'] != 1) {
    // Redirige si no está logueado o si no es administrador
    header('location: index.php');
    exit();
}

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
  </style>
</head>
<body>
  <?php include 'includes/sidebar.php'; ?>

  <div class="content-wrapper">
    <h2>Bienvenido al Panel de administrador</h2>
    <p>Seleccioná una opción del menú para comenzar.</p>

    <?php
		if (isset($_SESSION['user_data']) && is_array($_SESSION['user_data'])) {
		$user = $_SESSION['user_data'];
		echo "<div class='alert alert-info'>Bienvenido, <strong>{$user['first_name']} {$user['last_name']}</strong>. Estás logueado como <strong>" . ($user['type'] == 1 ? "Administrador" : "Usuario") . "</strong>.</div>";
		} else {
		echo "<div class='alert alert-warning'>No se pudo cargar la información del usuario.</div>";
		}
	?>

 <div style="display: flex; gap: 40px; justify-content: center; flex-wrap: wrap;">

    <div style="text-align: center;">
      <h2>Respaldo de los Archivos en la Nube</h2>
      <form method="POST" action="../backup_all.php" style="margin-bottom: 20px;">
        <button type="submit" class="btn btn-danger">
          <span class="glyphicon glyphicon-hdd"></span> Hacer respaldo TOTAL a Dropbox
        </button>
      </form>
    </div>

    <div style="text-align: center;">
      <h2>Respaldo de la Base de Datos en la Nube</h2>
      <form method="POST" action="../backup_db.php" style="margin-bottom: 20px;">
        <button type="submit" class="btn btn-info">
          <span class="glyphicon glyphicon-save"></span> Respaldar Base de Datos a Dropbox
        </button>
      </form>
    </div>

  </div>

  <center><h1>!!ADVERTENCIA!!</h1></center>
  <center><h2>Paciencia a la hora de realizar un respaldo es un proceso grande</h2></center>
  <center><p>(Limitaciones Del Servidor)</p></center>



  </div>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>
  
</body>
</html>
