<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/SolicitudCambioModel.php';
require_once __DIR__ . '/../model/OrdenCambioModel.php';

use Dompdf\Dompdf;

class ReporteControlador {
    private $solModel;
    private $ocModel;

    public function __construct() {
        session_start();
        $this->solModel = new SolicitudCambioModel();
        $this->ocModel  = new OrdenCambioModel();
    }

    public function index() {
        require __DIR__ . '/../views/reporte/indexVista.php';
    }

    public function generarPDF() {
        $solicitudes = $this->solModel->obtenerTodasLasSolicitudes();
        $ordenes     = $this->ocModel->obtenerTodasLasOrdenes();

        $html  = '<h1 style="text-align:center;">Reporte de Gestión de Cambios</h1>';
        $html .= '<h2>Solicitudes de Cambio</h2>';
        $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%">';
        $html .= '<tr><th>ID</th><th>Título</th><th>Fecha</th><th>Prioridad</th><th>Estado</th></tr>';
        foreach ($solicitudes as $s) {
            $html .= sprintf(
              '<tr>
                  <td>%d</td>
                  <td>%s</td>
                  <td>%s</td>
                  <td>%s</td>
                  <td>%s</td>
               </tr>',
               $s['id_solicitud'],
               htmlspecialchars(mb_strimwidth($s['titulo'],0,30,'…')),
               date('d/m/Y', strtotime($s['fecha_creacion'])),
               $s['prioridad'],
               $s['estado']
            );
        }
        $html .= '</table>';

        $html .= '<h2 style="margin-top:20px;">Órdenes de Cambio</h2>';
        $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%">';
        $html .= '<tr><th>ID</th><th>Título Solicitud</th><th>Fecha</th><th>Estado</th><th>Responsable</th></tr>';
        foreach ($ordenes as $o) {
            $html .= sprintf(
              '<tr>
                  <td>%d</td>
                  <td>%s</td>
                  <td>%s</td>
                  <td>%s</td>
                  <td>%s</td>
               </tr>',
               $o['id_orden'],
               htmlspecialchars(mb_strimwidth($o['titulo_solicitud'],0,30,'…')),
               date('d/m/Y', strtotime($o['fecha_creacion'])),
               $o['estado'],
               htmlspecialchars(mb_strimwidth($o['nombre_creador'],0,20,'…'))
            );
        }
        $html .= '</table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','portrait');
        $dompdf->render();
        $dompdf->stream('reporte_cambios.pdf', ['Attachment'=>true]);
        exit;
    }
}
