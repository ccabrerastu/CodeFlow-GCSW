<?php
require_once __DIR__ . '/../model/OrdenCambioModel.php';
require_once __DIR__ . '/../model/SolicitudCambioModel.php';

class OrdenCambioControlador {
    private $model, $solModel;

    public function __construct() {
        session_start();
        $this->model    = new OrdenCambioModel();
        $this->solModel = new SolicitudCambioModel();
    }

    public function index() {
        $status = $_SESSION['status_message'] ?? null;
        unset($_SESSION['status_message']);
        $ordenes = $this->model->obtenerTodasLasOrdenes();
        require __DIR__ . '/../views/ordenCambio/listarOrdenesCambioVista.php';
    }

    public function mostrarFormularioCrear() {
        $solPendientes = $this->solModel->obtenerSolicitudesPorEstado('Aprobada');
        $formData      = $_SESSION['form_data_orden']  ?? ['id_solicitud'=>'','descripcion_detalle'=>''];
        $formErrors    = $_SESSION['form_errors_orden'] ?? [];
        unset($_SESSION['form_data_orden'], $_SESSION['form_errors_orden']);
        require __DIR__ . '/../views/ordenCambio/crearEditarOrdenCambioVista.php';
    }

    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=OrdenCambio&a=mostrarFormularioCrear");
            exit;
        }

        $id_sol   = filter_input(INPUT_POST,'id_solicitud',FILTER_VALIDATE_INT);
        $detalle  = trim($_POST['descripcion_detalle'] ?? '');
        $formErrors = [];
        if (!$id_sol)           $formErrors['id_solicitud']        = "Seleccione una solicitud.";
        if (empty($detalle))    $formErrors['descripcion_detalle'] = "Detalle obligatorio.";

        if (!empty($formErrors)) {
            $_SESSION['form_data_orden']   = $_POST;
            $_SESSION['form_errors_orden'] = $formErrors;
            header("Location: index.php?c=OrdenCambio&a=mostrarFormularioCrear");
            exit;
        }

        $id_new = $this->model->crearOrden(
            $id_sol,
            $_SESSION['usuario']['id_usuario'],
            $detalle
        );

        $_SESSION['status_message'] = $id_new
            ? ['type'=>'success','text'=>"Orden #{$id_new} creada."]
            : ['type'=>'error','text'=>'Error al crear orden.'];

        header("Location: index.php?c=OrdenCambio&a=index");
        exit;
    }

    public function detalle($id_orden) {
        $orden = $this->model->obtenerOrdenPorId($id_orden);
        if (!$orden) {
            $_SESSION['status_message'] = ['type'=>'error','text'=>'Orden no encontrada.'];
            header("Location: index.php?c=OrdenCambio&a=index");
            exit;
        }
        require __DIR__ . '/../views/ordenCambio/detalleOrdenCambioVista.php';
    }

    public function eliminar($id_orden) {
        $ok = $this->model->eliminarOrden($id_orden);
        $_SESSION['status_message'] = $ok
            ? ['type'=>'success','text'=>'Orden eliminada.']
            : ['type'=>'error','text'=>'Error al eliminar.'];
        header("Location: index.php?c=OrdenCambio&a=index");
        exit;
    }
}
