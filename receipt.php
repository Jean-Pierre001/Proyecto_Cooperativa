<?php
require_once __DIR__ . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $liquidacion = $_POST['liquidacion'];
    $anticipo = $_POST['anticipo'];
    $coop = $_POST['coop'];
    $retiro = $_POST['retiro'];
    $total = $_POST['total'];
    $fecha = $_POST['fecha'];

    // Crear PDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('Sistema Cooperativa');
    $pdf->SetAuthor('Cooperativa La Comarca');
    $pdf->SetTitle('Recibo');
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetAutoPageBreak(TRUE, 20);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    // Línea vertical derecha “cortada”
    $margin_top = 20;
    $margin_bottom = 20;
    $x = $pdf->getPageWidth() - 15;
    $y1 = $margin_top;
    $y2 = $pdf->getPageHeight() - $margin_bottom;
    $pdf->SetLineWidth(0.5);
    $pdf->Line($x, $y1, $x, $y2);

    // Contenido HTML
    $html = '
    <div style="width:760px; margin:auto; padding:20px; font-family:Arial, sans-serif; font-size:14px;">

        <!-- Título y N° al mismo nivel -->
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
            <tr>
                <td style="text-align:left; vertical-align:middle;">
                    <div style="line-height:24px; font-size:10px; font-weight:bold;">
                        RECIBO DE RETIRO A CUENTA DE RESULTADOS
                    </div>
                </td>
                <td style="text-align:right; vertical-align:middle;">
                    <div style="line-height:24px; font-size:12px; font-weight:bold;">
                        N°..................
                    </div>
                </td>
            </tr>
        </table>

        <!-- Línea horizontal bajo la cooperativa -->
        <p style="text-align:center; font-weight:bold; margin:0 0 5px 0; border-bottom:2px solid #000; padding-bottom:5px;">
            COOP. DE TRABAJO LA COMARCA LTDA.
        </p>

        <br>
        <p style="text-align:center;">Matrícula Nacional N°: 58106 &nbsp;&nbsp;&nbsp;&nbsp; CUIT: 30716869373</p>

        <p><strong>APELLIDO Y NOMBRE:</strong> '.$nombre.'<span style="border-bottom:1px dotted #000; display:inline-block; width:300px;"></span></p>
        <p><strong>DNI:</strong> '.$dni.'<span style="border-bottom:1px dotted #000; display:inline-block; width:150px;"></span></p>
        <p>Liquidación: '.$liquidacion.'<span style="border-bottom:1px dotted #000; display:inline-block; width:150px;"></span></p>
        <p>Anticipo de Retorno:................. $'.$anticipo.'<span style="border-bottom:1px dotted #000; display:inline-block; width:100px;"></span></p>
        <p>Cooperativa 7%:....................... $'.$coop.'<span style="border-bottom:1px dotted #000; display:inline-block; width:100px;"></span></p>
        <p>Retiro a cuenta:......................... $'.$retiro.'<span style="border-bottom:1px dotted #000; display:inline-block; width:100px;"></span></p>
        <p>Recibí conforme la suma:..........$'.$total.'<span style="border-bottom:1px dotted #000; display:inline-block; width:100px;"></span></p>

        <p>Según liquidación precedente en concepto de anticipo de retornos por mi aporte como trabajador autónomo asociado.</p>

        <p><strong>Viedma,</strong> '.$fecha.'<span style="border-bottom:1px dotted #000; display:inline-block; width:200px;"></span></p>

        

    </div>
    <!-- Firmas lado a lado con líneas independientes -->
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:100px; text-align:center;">
        <br>
        <br>
        <br>
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
    ';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('recibo de socio.pdf', 'I');
} else {
    echo "Acceso no permitido.";
}
?>