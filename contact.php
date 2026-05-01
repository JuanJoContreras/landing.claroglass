<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metodo no permitido']);
    exit;
}

$nombre    = htmlspecialchars(trim($_POST['nombre']    ?? ''));
$email     = filter_var(trim($_POST['email']     ?? ''), FILTER_SANITIZE_EMAIL);
$telefono  = htmlspecialchars(trim($_POST['telefono']  ?? ''));
$region    = htmlspecialchars(trim($_POST['region']    ?? ''));
$comuna    = htmlspecialchars(trim($_POST['comuna']    ?? ''));
$producto  = htmlspecialchars(trim($_POST['producto']  ?? ''));
$como      = htmlspecialchars(trim($_POST['como']      ?? ''));
$mensaje   = htmlspecialchars(trim($_POST['mensaje']   ?? ''));
$gclid     = htmlspecialchars(trim($_POST['gclid']     ?? ''));

if (!$nombre || !$email || !$telefono) {
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email invalido']);
    exit;
}

$fecha = date('d-m-Y H:i:s');

$destinatarios = [
    'juanjose.contreras@claroglass.cl',
    'marketing.claroglass@gmail.com',
    'contacto@claroglass.cl'
];

$html = '<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:30px 0;">
  <tr><td align="center">
    <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.1);">

      <!-- HEADER -->
      <tr><td align="center" style="background:#0c2340;padding:28px 20px;">
        <img src="https://juanjocontreras.github.io/landing.claroglass/assets/logo-claroglass.png"
             alt="CLAROGLASS" width="70"
             style="display:block;margin:0 auto 10px;">
        <div style="font-size:24px;font-weight:bold;color:#c9a84c;letter-spacing:2px;">CLAROGLASS</div>
        <div style="font-size:11px;color:rgba(255,255,255,0.65);letter-spacing:3px;text-transform:uppercase;margin-top:4px;">Cierres Plegables de Cristal</div>
      </td></tr>

      <!-- TITULO -->
      <tr><td align="center" style="padding:28px 40px 10px;">
        <h2 style="margin:0;font-size:22px;color:#0c2340;">Formulario de Cotizaci&oacute;n</h2>
        <p style="margin:8px 0 0;color:#555;font-size:14px;">Un cliente ha completado el formulario de contacto.</p>
        <p style="margin:6px 0 0;color:#999;font-size:12px;">' . $fecha . '</p>
      </td></tr>

      <!-- DATOS DEL CLIENTE -->
      <tr><td style="padding:20px 40px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e0e0e0;border-radius:6px;overflow:hidden;">

          <tr><td style="background:#f8f8f8;padding:12px 20px;border-bottom:1px solid #e0e0e0;">
            <strong style="font-size:14px;color:#0c2340;">Datos del cliente</strong>
          </td></tr>

          <tr><td style="padding:16px 20px;font-size:14px;color:#555;line-height:1.9;">
            <strong style="color:#0c2340;">Nombre:</strong> ' . $nombre . '<br>
            <strong style="color:#0c2340;">Tel&eacute;fono:</strong> ' . $telefono . '<br>
            <strong style="color:#0c2340;">Email:</strong> <a href="mailto:' . $email . '" style="color:#1a6fa8;">' . $email . '</a><br>
            <strong style="color:#0c2340;">Regi&oacute;n:</strong> ' . ($region ?: '—') . '<br>
            <strong style="color:#0c2340;">Comuna:</strong> ' . ($comuna ?: '—') . '<br>
            <strong style="color:#0c2340;">Producto de inter&eacute;s:</strong> ' . ($producto ?: '—') . '<br>
            <strong style="color:#0c2340;">C&oacute;mo nos conocio:</strong> ' . ($como ?: '—') . '
        <br><strong style="color:#0c2340;">GCLID:</strong> ' . ($gclid ?: '-') . '
          </td></tr>

        </table>
      </td></tr>

      <!-- MENSAJE -->
      <tr><td style="padding:0 40px 20px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e0e0e0;border-radius:6px;overflow:hidden;">

          <tr><td style="background:#f8f8f8;padding:12px 20px;border-bottom:1px solid #e0e0e0;">
            <strong style="font-size:14px;color:#0c2340;">Mensaje:</strong>
          </td></tr>

          <tr><td style="padding:16px 20px;font-size:14px;color:#555;line-height:1.7;background:#fafafa;">
            ' . nl2br($mensaje) . '
          </td></tr>

        </table>
      </td></tr>

      <!-- FOOTER -->
      <tr><td align="center" style="padding:20px 40px 30px;border-top:1px solid #f0f0f0;">
        <a href="https://www.claroglass.cl"
           style="color:#c9a84c;font-size:13px;text-decoration:none;">www.claroglass.cl</a>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>';

$asunto  = 'Cotizacion CLAROGLASS ' . $fecha . ' - ' . $telefono;
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: CLAROGLASS Contacto <noreply@claroglass.cl>\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$errores = 0;
foreach ($destinatarios as $dest) {
    if (!mail($dest, $asunto, $html, $headers)) { $errores++; }
}

if ($errores === 0) {
    echo json_encode(['success' => true,  'message' => 'Mensaje enviado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al enviar el correo']);
}
?>
