<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==================== CONFIGURACIÓN ====================
define('DROPBOX_CLIENT_ID', 'htnpfhdp0fiwj5a');
define('DROPBOX_CLIENT_SECRET', 'c9c7u5rxniyysjo');
// Usa aquí el refresh token correcto que te dio Dropbox (ejemplo abajo)
define('DROPBOX_REFRESH_TOKEN', 'RK12uPOEoYwAAAAAAAAAAZJ104bKLOmpF1Mm2gmlQ2JJ8Vjz8Ueu2KTmO9LEhFSa');

// ==================== FUNCIONES ====================

function obtenerAccessToken() {
    try {
        if (isset($_SESSION['dropbox_access_token']) && isset($_SESSION['dropbox_token_time'])) {
            $tiempoTranscurrido = time() - $_SESSION['dropbox_token_time'];
            // Renovar token si pasaron más de 3h 50m (13800 segundos)
            if ($tiempoTranscurrido < 13800) {
                return $_SESSION['dropbox_access_token'];
            }
        }

        $nuevoToken = refrescarAccessToken();
        $_SESSION['dropbox_access_token'] = $nuevoToken;
        $_SESSION['dropbox_token_time'] = time();
        return $nuevoToken;

    } catch (Exception $e) {
        // Aquí podés loguear el error o manejarlo
        throw new Exception("Error al obtener access token: " . $e->getMessage());
    }
}

function refrescarAccessToken() {
    $url = 'https://api.dropbox.com/oauth2/token';
    $postFields = http_build_query([
        'grant_type' => 'refresh_token',
        'refresh_token' => DROPBOX_REFRESH_TOKEN,
        'client_id' => DROPBOX_CLIENT_ID,
        'client_secret' => DROPBOX_CLIENT_SECRET,
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
    ]);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) {
        throw new Exception("Error al refrescar token: $response");
    }

    $data = json_decode($response, true);
    if (!isset($data['access_token'])) {
        throw new Exception("No se recibió access_token al refrescar token.");
    }

    return $data['access_token'];
}

function subirCarpetaADropbox($carpetaLocal, $rutaDropboxDestino) {
    try {
        $accessToken = obtenerAccessToken();

        $app = new DropboxApp(DROPBOX_CLIENT_ID, DROPBOX_CLIENT_SECRET, $accessToken);
        $dropbox = new Dropbox($app);

        if (!is_dir($carpetaLocal)) {
            throw new Exception("La carpeta local '$carpetaLocal' no existe.");
        }

        $archivos = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($carpetaLocal, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($archivos as $archivo) {
            if ($archivo->isFile()) {
                $rutaLocal = $archivo->getPathname();
                $rutaRelativa = str_replace($carpetaLocal, '', $rutaLocal);
                $rutaDropbox = rtrim($rutaDropboxDestino, '/') . '/' . ltrim(str_replace('\\', '/', $rutaRelativa), '/');

                try {
                    $dropbox->upload($rutaLocal, $rutaDropbox, ['autorename' => true]);
                } catch (Exception $e) {
                    // Podés loguear error y seguir con los demás archivos
                    throw new Exception("Error al subir '$rutaLocal': " . $e->getMessage());
                }
            }
        }
    } catch (Exception $e) {
        // Manejo general de errores
        throw new Exception("Error en subirCarpetaADropbox: " . $e->getMessage());
    }
}
/*
{"access_token": "sl.u.AF2Ifkd0fvIVWKPm9OceSc66xRdOPVEz19O_ZbD8jykOT8TE0bA6-qNqvfWAG_WAugSkwI86b6dYliaY0iiTzBtCIbk5xs2iHQTnXct-prgefV1DsfRR330j8ICO1NUU1XLAF_W7tCgHSL0ggaOOD7tsIjxIQCfDDWngx_fwMqf-rUgf1FqUeETcf8tFZeXgsoh703cuTKWw67Blaoc2h9zCy-rxdNYaUAkYhPgfW8t8dbzUQ7G4uxkyc-9Cx1ORrdbgPbcQU4jMZw0arqdkAgVXWilpaGaO83s7j4qb7jaE1YBr8fSC0G6e2So_ZfBiM457xMhOaR7uk6Ckv4wPl0LgjAU5xaDp54621N098S866NeM0SJ9FXpT9Co0wdMQeoqWTJrRpwnajITdIxS53RlzMrCyRlXP9hGzce9ISA13PY3jDlgSuIW3RxaYikqYPnLyfM8Uex6wGKVJRGso65Zu-PZDAGBQK-SHDo4oB-FO1T6aFyHpi2pUyyu_ZhkMMdpFBbhSymzQYueAHDMINilFpl7W3ADo_eIxkjxXFmqxcbLWyPLzgkD8m8fNzDU1zwz3WrO__ptBiKzJCUZpRT-78qswAsmizXUvkioNLHz5WMd1TOGNQACSju-AlQ3aZrWRIGRPHcrBVERjNp81oPnSv20Fkkxckla278KI9GTYIrskPP0DShW2Te1_TbQ5RPdSerVhCJu49nio_yICnC9G12sAwMJts4EvOlmsmp9uUypKbOvIjlPKD7RnPTdbjUHTpUCtwU4DpqzR_zjIyopPPDO9CSsBIR_JCFtTpe3YFguJ1fOuJGE31wMk14g7AMoZQs2GJ45XP7YPZ6YOdeR66gfzfpb3bEuCmv4YL9EoTIJ3J3vz0rA92mbS9RY3xwCezxzV6QMtznEvfvB8Q3oc2oEbHf1OpMSMFqQLrIBLVTjk3qTk8ZL4o1Ve1DJQY6QzeNA485ZjqOr0weiieJ9uqrV0h0pZ4eUGy8sYkbZAmKxZ8orkj1p77IUNQE_1V1TtJ8D-QzAZNn79ynpu03Yj_4ddS2yPshO-YyxWBQ__Jdftzmsvq8MM7sczay9PhOjYr2u2JuPbTOXeCldRzyU85jtO4we6G0xJv8qxdutY7_tdnZ6rRaVSyc5qrB8ZvuL8mGk1hccHBFy2sk4bnMkUOe97vUChuVAr_ocM1v8J3UqiK_HoEJc0WJFtxUWiW4gWJmNWTY69XfoL6yUWloN36YzyOOBfpmCpX218pho-liySOnbf6rIHiZ5IcJGwEBJoKHy_WQbTHI_gyfNSNeFZSQIqQeXvgkvBBu6evUDDI3Kx8r6S0riEHSZKwxnSOKnMhKYp8HliuGTBbY0hMbwJzJzm4kerBFGdMCL6-weOk2WjPIePw58x3cDHGHFNxnACZRHn45_6LzkeQSn5VdPgeG_E1OF82J2FmNGhF58Sxw", "token_type": "bearer", "expires_in": 14400, "refresh_token": "RK12uPOEoYwAAAAAAAAAAZJ104bKLOmpF1Mm2gmlQ2JJ8Vjz8Ueu2KTmO9LEhFSa", "scope": "account_info.read files.content.read files.content.write files.metadata.read files.metadata.write", "uid": "1722411555", "account_id": "dbid:AACxzyFl3qbXBJc3tx7WmwJ1u4s4XXSz5CQ"}
*/