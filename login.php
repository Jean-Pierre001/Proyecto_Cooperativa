<?php include 'includes/session.php'; ?>

<?php
  if (isset($_SESSION['user_data'])) {
    // Redireccionar según tipo de usuario
    switch ($_SESSION['user_data']['type']) {
      case 1:
        header('location: index.php');
        exit();
      default:
        header('location: index.php');
        exit();
    }
  }
?>

<?php include 'includes/header.php'; ?>

<body class="hold-transition login-page">
  <link rel="stylesheet" href="assets/custom/login.css">
  <div class="background-image"></div>

  <div class="login-box custom-login-box">
    <?php
      if (isset($_SESSION['error'])) {
        echo "
          <div class='alert alert-danger text-center'>
            " . htmlspecialchars($_SESSION['error']) . "
          </div>
        ";
        unset($_SESSION['error']);
      }
      if (isset($_SESSION['success'])) {
        echo "
          <div class='alert alert-success text-center'>
            " . htmlspecialchars($_SESSION['success']) . "
          </div>
        ";
        unset($_SESSION['success']);
      }
    ?>
    <div class="login-box-body">
      <h3 class="login-box-msg" style="font-weight:700; margin-bottom: 20px;">Iniciar sesión para acceder al sistema</h3>

      <form action="verify.php" method="POST" novalidate>
        <div class="form-group has-feedback">
            <input type="text" class="form-control input-lg" name="first_name" placeholder="nombre" required maxlength="100" autocomplete="username">
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input type="password" class="form-control input-lg" name="password" placeholder="Contraseña" required maxlength="100" autocomplete="current-password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>

        <div class="form-group" style="display:flex; justify-content:center; margin-bottom: 15px;">
          <div class="g-recaptcha" data-sitekey="6Lcw6oErAAAAAFKEv2zSkCJ4QZfJraaXJPJQrp5p"></div>
        </div>

        <div class="row" style="margin-top: 15px;">
          <div class="col-xs-12">
            <button type="submit" class="btn btn-primary btn-block btn-flat btn-lg" name="login">
              <i class="fa fa-sign-in"></i> Sign In
            </button>
          </div>
        </div>
      </form>
      <br>
    </div>
  </div>

  <!-- Script necesario para Google reCAPTCHA -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php include 'includes/scripts.php' ?>

</body>
</html>
