<?php
include 'includes/session.php';
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Generar Recibo</title>
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
    .recibo {
      width: 800px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border: 1px solid #000;
      font-family: Arial, sans-serif;
      font-size: 14px;
      position: relative;
    }
    .recibo h4 {
      text-align: center;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .recibo .row-line {
      margin: 10px 0;
    }
    .recibo label {
      font-weight: bold;
      margin-right: 10px;
    }
    .recibo input {
      border: none;
      border-bottom: 1px dotted #000;
      outline: none;
      background: transparent;
      width: auto;
      display: inline-block;
    }
    .firma {
      text-align: center;
      margin-top: 40px;
    }
    .firma span {
      border-top: 1px dashed #000;
      display: inline-block;
      padding-top: 5px;
    }
  </style>
</head>
<body>
  <?php include 'includes/sidebar.php'; ?>
  <div class="content-wrapper">
    <div class="recibo">
      <form method="post" action="receipt.php">
        <h4>RECIBO DE RETIRO A CUENTA DE RESULTADOS</h4>

        <p><strong>COOP. DE TRABAJO LA COMARCA LTDA.</strong></p>
        <p>Matrícula Nacional N°: 58106 &nbsp;&nbsp;&nbsp;&nbsp; CUIT: 30716869373</p>

        <div class="row-line">
          <label>APELLIDO Y NOMBRE:</label>
          <input type="text" name="nombre" required>
        </div>

        <div class="row-line">
          <label>DNI:</label>
          <input type="text" name="dni" required>
        </div>

        <div class="row-line">
          <label>Liquidación:</label>
          <input type="text" name="liquidacion" required>
        </div>

        <div class="row-line">
          <label>Anticipo de Retorno:</label> $
          <input type="number" step="0.01" name="anticipo" required>
        </div>

        <div class="row-line">
          <label>Cooperativa 7%:</label> $
          <input type="number" step="0.01" name="coop" required>
        </div>

        <div class="row-line">
          <label>Retiro a cuenta:</label> $
          <input type="number" step="0.01" name="retiro">
        </div>

        <div class="row-line">
          <label>Recibí conforme la suma:</label> $
          <input type="number" step="0.01" name="total" readonly>
        </div>

        <p>según liquidación precedente en concepto de anticipo de retornos por mi aporte como trabajador autónomo asociado.</p>

        <div class="row-line">
          Viedma, <input type="text" name="fecha" placeholder="6 de Mayo de 2025" required>
        </div>

        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:100px; text-align:center;">
            <tr>
                <td style="width:50%; text-align:center;">
                    <div style="width:120px; border-top:1px dashed #000; margin:auto;"></div>
                    <div style="margin-top:5px;">FIRMA PRESIDENTE</div>
                </td>
                <td style="width:50%; text-align:center;">
                    <div style="width:120px; border-top:1px dashed #000; margin:auto;"></div>
                    <div style="margin-top:5px;">FIRMA ASOCIADO</div>
                </td>
            </tr>
        </table>

        

        <br>
        <button type="submit" class="btn btn-success btn-block">
          <span class="glyphicon glyphicon-download-alt"></span> Generar PDF
        </button>
      </form>
    </div>
  </div>
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>


<script>
  const retiroInput = document.querySelector('input[name="retiro"]');
  const coopInput = document.querySelector('input[name="coop"]');
  const anticipoInput = document.querySelector('input[name="anticipo"]');
  const totalInput = document.querySelector('input[name="total"]');

  function calcularTotal() {
    const retiro = parseFloat(retiroInput.value) || 0;
    const coop = parseFloat(coopInput.value) || 0;
    const anticipo = parseFloat(anticipoInput.value) || 0;

    const total = anticipo - (coop + retiro);
    totalInput.value = total.toFixed(2);
  }

  retiroInput.addEventListener('input', calcularTotal);
  coopInput.addEventListener('input', calcularTotal);
  anticipoInput.addEventListener('input', calcularTotal);

  window.addEventListener('load', calcularTotal);
</script>


</body>
</html>
