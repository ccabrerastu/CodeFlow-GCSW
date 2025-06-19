<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../model/SolicitudCambioModel.php';
require_once __DIR__ . '/../model/OrdenCambioModel.php';

use Dompdf\Dompdf;

class ReporteControlador {
    private $solModel;
    private $ocModel;
    private $userModel;

    public function __construct() {
        session_start();
        $this->solModel  = new SolicitudCambioModel();
        $this->ocModel   = new OrdenCambioModel();
        $this->userModel = new UsuarioModel();
    }

    public function index() {
        require __DIR__ . '/../views/reporte/indexVista.php';
    }

    public function generarPDF() {
        $username     = $_SESSION['usuario']['nombre_usuario'] ?? null;
        $usuarioInfo  = $username ? $this->userModel->findByNombreUsuario($username) : null;
        $nombreUsuario = htmlspecialchars($usuarioInfo['nombre_completo'] ?? 'Usuario');

        $solicitudes = $this->solModel->obtenerTodasLasSolicitudes();
        usort($solicitudes, function($a, $b){
            return $a['id_solicitud'] <=> $b['id_solicitud'];
        });

        $ordenes = $this->ocModel->obtenerTodasLasOrdenes();
        usort($ordenes, function($a, $b){
            return $a['id_orden'] <=> $b['id_orden'];
        });
        usort($ordenes, function($a, $b) {
            return $a['id_solicitud'] <=> $b['id_solicitud'];
        });

        $logoPath = __DIR__ . '/../public/images/upt.png';
        if (file_exists($logoPath)) {
            $b64 = base64_encode(file_get_contents($logoPath));
            $logoTag = "<div class=\"center\">
                          <img src=\"data:image/png;base64,{$b64}\" class=\"logo\" alt=\"Logo UPT\" />
                        </div>";
        } else {
            $logoTag = "";
        }

        $css = <<<CSS
        <style>
        @page { margin: 50px 40px; }
        body { font-family: "Times New Roman", serif; font-size: 12pt; margin:0; padding:0; }
        .center { text-align: center; }
        .justify { text-align: justify; }
        .logo { width: 120px; margin-bottom: 20px; }
        h1, h2, h3 { text-align: center; margin: 5px 0; }
        h1 { font-size: 20pt; }
        h2 { font-size: 16pt; }
        .small { font-size: 10pt; }
        .cover p { margin: 8px 0; }
        .page-break { page-break-after: always; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background-color: #eee; }
        </style>
        CSS;

        $fechaHoy = date('d/m/Y');
        $caratula = <<<HTML
        <div class="cover">
        {$logoTag}
        <h1>UNIVERSIDAD PRIVADA DE TACNA</h1>
        <h2>FACULTAD DE INGENIERÍA</h2>
        <h3>Escuela Profesional de Ingeniería de Sistemas</h3>
        <br>
        <center><p class="small"><em>Sistema Web para la Gestión de Configuración de Software – CodeFlow</em></p></center>
        <center><p>Generado por: <strong>{$nombreUsuario}</strong></p></center>
        <center><p>Fecha de emisión: <strong>{$fechaHoy}</strong></p></center>
        </div>
        <div class="page-break"></div>
        HTML;

        $solTabla = '<h2>Solicitudes de Cambio</h2><table>
        <tr>
            <th style="width:5%">ID</th>
            <th style="width:20%">Proyecto</th>
            <th style="width:35%">Título</th>
            <th style="width:15%">Fecha</th>
            <th style="width:15%">Prioridad</th>
            <th style="width:10%">Estado</th>
        </tr>';
        foreach ($solicitudes as $s) {
            $solTabla .= sprintf(
            '<tr>
                <td>%d</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
            </tr>',
            $s['id_solicitud'],
            htmlspecialchars(mb_strimwidth($s['nombre_proyecto'],0,30,'…')),
            htmlspecialchars(mb_strimwidth($s['titulo'],0,50,'…')),
            date('d/m/Y', strtotime($s['fecha_creacion'])),
            $s['prioridad'],
            $s['estado']
            );
        }
        $solTabla .= '</table>';

        $salto = '<div class="page-break"></div>';

        $ocTabla = '<h2>Órdenes de Cambio</h2><table>
        <tr>
            <th style="width:5%">ID</th>
            <th style="width:10%">SC #</th>
            <th style="width:35%">Título Solicitud</th>
            <th style="width:15%">Fecha</th>
            <th style="width:15%">Estado</th>
            <th style="width:20%">Responsable</th>
        </tr>';
        foreach ($ordenes as $o) {
            $ocTabla .= sprintf(
            '<tr>
                <td>%d</td>
                <td>%d</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
            </tr>',
            $o['id_orden'],
            $o['id_solicitud'],
            htmlspecialchars(mb_strimwidth($o['titulo_solicitud'],0,50,'…')),
            date('d/m/Y', strtotime($o['fecha_creacion'])),
            $o['estado'],
            htmlspecialchars(mb_strimwidth($o['nombre_completo'] ?? $o['nombre_creador'],0,25,'…'))
            );
        }
        $ocTabla .= '</table>';

        if (ob_get_length()) { ob_end_clean(); }
        $dompdf = new Dompdf();
        $dompdf->loadHtml($css . $caratula . $solTabla . $salto . $ocTabla);
        $dompdf->setPaper('A4','portrait');
        $dompdf->render();
        $dompdf->stream('Reporte_Cambios_'.date('Ymd').'.pdf', ['Attachment'=>true]);
        exit;
    }
}
