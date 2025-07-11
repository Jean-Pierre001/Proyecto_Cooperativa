<?php include 'includes/session.php'; ?>
<?php
  if(isset($_SESSION['user'])){
    // Redireccionar según tipo de usuario
    switch ($_SESSION['user']['type']) {
      case 1:
        header('location: index.php');
        break;
      default:
        header('location: index.php');
    }
  }
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition login-page">
  <link rel="stylesheet" href="assets/custom/login.css">
  <div class="background-image"></div>

  <div class="login-box custom-login-box">
    <?php
      if(isset($_SESSION['error'])){
        echo "
          <div class='alert alert-danger text-center'>
            ".$_SESSION['error']."
          </div>
        ";
        unset($_SESSION['error']);
      }
      if(isset($_SESSION['success'])){
        echo "
          <div class='alert alert-success text-center'>
            ".$_SESSION['success']."
          </div>
        ";
        unset($_SESSION['success']);
      }
    ?>
    <div class="login-box-body">
      <h3 class="login-box-msg" style="font-weight:700; margin-bottom: 20px;">Sign in to access the system</h3>

      <form action="verify.php" method="POST">
        <div class="form-group has-feedback">
          <input type="email" class="form-control input-lg" name="email" placeholder="Email" required>
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input type="password" class="form-control input-lg" name="password" placeholder="Password" required>
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <button type="submit" class="btn btn-primary btn-block btn-flat btn-lg" name="login">
              <i class="fa fa-sign-in"></i> Sign In
            </button>
          </div>
        </div>
      </form>
      <br>
      <a href="index.php" class="text-center"><i class="fa fa-home"></i> Home</a>
    </div>
  </div>

<?php include 'includes/scripts.php' ?>

</body>
</html>
