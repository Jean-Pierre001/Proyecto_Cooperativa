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
    $accessToken = 'sl.u.AF3aFZV44tcXHdR2eJH1LX-H0TukCGbGIlNQN3RNYPA4FFXSBDWxV5kNqA-u8gEwTp1U36WYo2QffYAVyTGLtmsFEoDjSPlH2NOm5sPPQda7iBTN8enkAlab5DI0etfG-eG1-3a6nqSJhONVz1aRnPR0EZxnPBQIrkuQgsBAfF7XJ-MpqZUje-whb94YP-2ZIP9cSPfAh0RL_WefEkKTf-RoTwSP8Tg9B_4oqo4yXqu9RV6CTaeJn25kFSniCmWvv5aM7a0PJzWxMNwkkE7hsNi57ovFU9zCQnwqKXar7GEsIGXkeZItKcn6cUo2op-3eM68pS_1vXs_aR6cRn6ypo9uqYVuZkeoNo_90ClCmtAC9JjirHWuzL_WZXVLGmGMJi2Rl-LCDgWgD5tzCkkrj9BuxdkeQy_IcPtplnvn1F5SKbCMOEzHmj9AYFot4CjUw59h8Ck60_fkioBOa3olA5RnZWhdNkYAOmgFzPAnLPGgFX71gefceQj4IioV1h4tuIisUAjX6RYczotvrU-m84q3-OGLVr5KAhQtYDj_KriHihvkenVvxa6AgJgCU0PUOzmstjSdpnQle6AB15l2MwE6UdZTp0AkkZp7R05vx9Illv0goF3LN7Wv-fRrLqCCenzTaV0KgLHxo3nASF7lIE95iO846ESpHppmf2SujwjRu1I4Yopg9ld4dEUvvEYFfm1-ZHBF4hUWFjB6VfdK9i1p38IbT09FpYmvrstPhKQF5f0rAXuthMCJGQIVhyE-XN2VbZ4MlDmntcKP5udeAEEMgfYW55FWBqpupAhbX03IDkNizSjdvKKAwHNJD1aHYX7tKYGOE5icKSar8T_7sW8TIXv6AC0DpMxcIsp346q08E0CMkp7pwxOv2G_rW2l8glW3WP6SfSXp88xsCHFdoIdN7pIRfF-j9PvICE1rpMU5u8iowcpBtKvEW8otTPBO9_2mViArfN1EUZC8HIwSthp2ISGDdZWkmmgSM8WsV9GNo9iXunBxa1VYU2iVnSbrV650n7GDB6A_cg0kCOCefj_-bgjJzstXi_2ohZBTs2mv1igsHVeVbwJ4tewNjjL5SOiNWwqoDZaRq-SKxulXIYm2xTSujT2ABg52BwiK7i7U77StW00nGzb7sVMiTvk5f5ML6u5oRvRY2jri3h0IyYqpby-T0WTTlkB_7Tvv49g72zRl431IPxFKBl0_U_d1byB0UyjY-pR5xWpdz-cGFuDnXo1rAA_AL-MANk1uC3cLu6AQ27FLd8_VkxdvuH5YSkKp0bj6EJKwwKXdFuk8ry9tjzxG7ZDsQ1Mgk-1B4ZG6_7nPsWnaW7MNLLIZGOcVTPt44_FyAS2kY_WQVWwByCuIElNf4RDF8kj18X_UZTg0lBHFc62VYhjRWkYHD1BWYcS1wiIlMebmEpAScqHkuPRd4NrHdRvMoqVzbz_ePRepQ';
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
