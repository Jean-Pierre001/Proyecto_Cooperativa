<?php
include 'includes/session.php';
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

require_once 'includes/dropbox_helper.php';

$host = 'localhost';
$dbname = 'cooperativa';
$user = 'root';
$pass = ''; // contraseña si la tenés

$fecha = date('Y-m-d_H-i-s');
$backupDir = __DIR__ . "/BACKUP_DATABASE";

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$archivoLocal = $backupDir . "/respaldo_$fecha.sql";
$archivoDropbox = "/respaldo_total/base_datos/respaldo_$fecha.sql";

// Ruta completa a mysqldump
$mysqldump = '"C:\\xampp\\mysql\\bin\\mysqldump.exe"';

// Comando sin redireccionar a archivo
$comando = "$mysqldump -h$host -u$user " . ($pass ? "-p$pass " : "") . "$dbname";

$sqlDump = shell_exec($comando);

if (!$sqlDump) {
    $_SESSION['error'] = "Error al ejecutar mysqldump.";
    header('Location: folders.php');
    exit();
}

// Guardar contenido en archivo
file_put_contents($archivoLocal, $sqlDump);

// Subir a Dropbox
try {
    $accessToken = 'sl.u.AF1e1QU5sY4WDG65hCSijojlgGWbC48wKm5zovUeLU45Sb3Owilad6ZllisVKZ_2rlnD42m5jDGwoor4WOoxxq4gC2WIjnCINd6nP6TB8PHFwSkeK4fgqpZxU39RFewqlrzVNS04uOY_OgtlNDldyxBUFugYzh3b9QBUQjATNCcl6fnteyZ5xgA-VGtAxY6n6TCJZe1TLVTW41T7-Y8JO6NYT6QHOca4gdidT7FtQN0BSI1RA4GyBXceaCR4QYBkBnCljSEuuZppYtOaJHmGzogPmAAXbjd5pQCO9dHv4U77flCweSK_soCsr6HV-apF_HgJHK2DCtBpCxlEQqFmuN0mFIvRq1wgicG9j6L4TYwLBqZnyS1-6XmknX_GkVGsXy4tQzNP4dlsMJY6s-6D7uDuo1A1gzPcPo7FnYMVYy3WBIlD4SRtWQbOJR51YmreWflsLfBYjxWIBvamVPHsQFj3iB9CjgGbsxgQm-xoh5jHbILrD0Pc9pVdRn-Ydwkx3EdMVHSx2WKPBYnO51nXDkn5LWkUmIAM3Te5wUzd5m_rQ3ZqG9siA8h-4IRwYwdeTTONRggKQ9pHweJ0lRMvexif1JZ4UFaUQrxbFhsFq00SuNbLqPIF8m9deaYr_dl4dRLKlnZ4b1749tV1Js1xjb587ceR2f2y9-7hupIvUJUsiSKIM8JjiLi3svgPBpJ1gwm7ob_ZebOgbq887BOB2_E1rKu98LfOLpA-rX3tKMtaiO5eh0XZ4d5AovZNeAJcx3REUe-hNIPRb3E_DnanK_WK5RwA82LCGUh0HqOCFUcAL2TWW3NPqcxWPje463RzT093jZwO-sGxP0oF3ffuMTiFB78wjDadyBe53gIEK59S_cqabanLIt7IHq6AsP_RNq_krEqDna0yq7V4FTfx1ozHXl_HGiUPQZcHbVgbW74sgwyHEGdc7dyyYddxIUPWYrZMugWSdafxVcc34eCWbDLAi34VEkm2VuHIVLLFikTKhKIfAbE9iAmPLwt9DDlG6BP8-YfKGiig23QhPyQvyz1hGV1ov1A5SSa8QMKgIfpkXigSiRr8LzmGG82QYYi7IVy1M8acx7kkO6eo0Sp9pPll_BaLq-ZxdUw-fs_hT4KNXzOwl8PvrmlRptuzGDZqi90MLSHAcyg6du9kL86yQjJf4JYKz0rRVT6r4wd0uVEAXyj6DIYJQsReTolcMM10faNZVMhfQGX-C-ZqBFG13PbKiplWoxlkgryzWdoUlG4XwHdA_BAYFXLjemAB9n-RD4rQdx-qliK6aOjTM2dpxKuZpCc0KJGAj7LB7bw8XGQ5p0HeXsiazbU4LvSnqc3JzUuFsER4aI1hhVKx8cqvFaQ67tvYR5Y87h0UwPO1HNzoy_0qHskhGJmYNsDiSsmkm_Yg71qPcIQ78uqXDzoR14as94inlcbBHAt67izw0RtYyQ';
    $app = new \Kunnu\Dropbox\DropboxApp("", "", $accessToken);
    $dropbox = new \Kunnu\Dropbox\Dropbox($app);

    $dropbox->upload($archivoLocal, $archivoDropbox, ['autorename' => true]);

    $_SESSION['success'] = "Respaldo de base de datos guardado en Dropbox exitosamente.";
} catch (Exception $e) {
    $_SESSION['error'] = "Error al subir respaldo a Dropbox: " . $e->getMessage();
}

// Eliminar archivo local temporal
unlink($archivoLocal);

header('Location: folders.php');
exit();
