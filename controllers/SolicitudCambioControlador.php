<?php
// controllers/SolicitudCambioControlador.php

require_once __DIR__ . '/../model/SolicitudCambioModel.php';
require_once __DIR__ . '/../model/ProyectoModel.php';

class SolicitudCambioControlador {
    private $model;
    private $proyectoModel;

    public function __construct() {
        session_start();
        $this->model = new SolicitudCambioModel();
        $this->proyectoModel = new ProyectoModel();
    }

    public function index() {
        $status = $_SESSION['status_message'] ?? null;
        unset($_SESSION['status_message']);
        $solicitudes = $this->model->obtenerTodasLasSolicitudes();
        require __DIR__ . '/../views/solicitudCambio/listarSolicitudesVista.php';
    }

    public function mostrarFormularioCrear() {
        $formData   = $_SESSION['form_data_solicitud']   ?? [
            'id_proyecto'=>'',
            'titulo'=>'',
            'descripcion'=>''
        ];
        $formErrors = $_SESSION['form_errors_solicitud'] ?? [];
        unset($_SESSION['form_data_solicitud'], $_SESSION['form_errors_solicitud']);

        $proyectos = $this->proyectoModel->obtenerTodosLosProyectos();

        require __DIR__ . '/../views/solicitudCambio/crearEditarSolicitudVista.php';
    }

    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=SolicitudCambio&a=mostrarFormularioCrear");
            exit;
        }

        $id_proyecto    = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT);
        // Ahora leemos la sesión tal como guarda el login:
        $id_solicitante = $_SESSION['id_usuario'] ?? null;
        $titulo         = trim($_POST['titulo'] ?? '');
        $descripcion    = trim($_POST['descripcion'] ?? '');

        $formErrors = [];
        if (!$id_proyecto) {
            $formErrors['id_proyecto'] = "Debe seleccionar un proyecto.";
        }
        if (empty($titulo)) {
            $formErrors['titulo'] = "El título es obligatorio.";
        }

        if ($formErrors) {
            $_SESSION['form_data_solicitud']   = $_POST;
            $_SESSION['form_errors_solicitud'] = $formErrors;
            header("Location: index.php?c=SolicitudCambio&a=mostrarFormularioCrear");
            exit;
        }

        $newId = $this->model->crearSolicitud(
            $id_proyecto,
            $id_solicitante,
            $titulo,
            $descripcion
        );

        $_SESSION['status_message'] = $newId
            ? ['type'=>'success','text'=>'Solicitud creada exitosamente.']
            : ['type'=>'error','text'=>'Error al crear la solicitud.'];

        header("Location: index.php?c=SolicitudCambio&a=index");
        exit;
    }

    public function detalle($id_solicitud) {
        $sol = $this->model->obtenerSolicitudPorId($id_solicitud);
        if (!$sol) {
            $_SESSION['status_message'] = ['type'=>'error','text'=>'Solicitud no encontrada.'];
            header("Location: index.php?c=SolicitudCambio&a=index");
            exit;
        }
        require __DIR__ . '/../views/solicitudCambio/detalleSolicitudVista.php';
    }

    public function mostrarFormularioEditar($id_solicitud) {
        $sol = $this->model->obtenerSolicitudPorId($id_solicitud);
        if (!$sol) {
            $_SESSION['status_message'] = ['type'=>'error','text'=>'Solicitud no encontrada.'];
            header("Location: index.php?c=SolicitudCambio&a=index");
            exit;
        }

        $formData   = $_SESSION['form_data_solicitud']   ?? $sol;
        $formErrors = $_SESSION['form_errors_solicitud'] ?? [];
        unset($_SESSION['form_data_solicitud'], $_SESSION['form_errors_solicitud']);

        $proyectos = $this->proyectoModel->obtenerTodosLosProyectos();

        require __DIR__ . '/../views/solicitudCambio/crearEditarSolicitudVista.php';
    }

    public function editar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=SolicitudCambio&a=index");
            exit;
        }

        $id_solicitud = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
        $id_proyecto  = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT);
        $titulo       = trim($_POST['titulo'] ?? '');
        $descripcion  = trim($_POST['descripcion'] ?? '');

        $formErrors = [];
        if (!$id_solicitud) {
            $formErrors['general'] = "ID inválido.";
        }
        if (!$id_proyecto) {
            $formErrors['id_proyecto'] = "Debe seleccionar un proyecto.";
        }
        if (empty($titulo)) {
            $formErrors['titulo'] = "El título es obligatorio.";
        }

        if ($formErrors) {
            $_SESSION['form_data_solicitud']   = $_POST;
            $_SESSION['form_errors_solicitud'] = $formErrors;
            header("Location: index.php?c=SolicitudCambio&a=mostrarFormularioEditar&id_solicitud={$id_solicitud}");
            exit;
        }

        $ok = $this->model->actualizarSolicitud(
            $id_solicitud,
            $titulo,
            $descripcion
        );

        $_SESSION['status_message'] = $ok
            ? ['type'=>'success','text'=>'Solicitud actualizada.']
            : ['type'=>'error','text'=>'Error al actualizar la solicitud.'];

        header("Location: index.php?c=SolicitudCambio&a=index");
        exit;
    }

    public function eliminar($id_solicitud) {
        $ok = $this->model->eliminarSolicitud($id_solicitud);
        $_SESSION['status_message'] = $ok
            ? ['type'=>'success','text'=>'Solicitud eliminada.']
            : ['type'=>'error','text'=>'Error al eliminar la solicitud.'];
        header("Location: index.php?c=SolicitudCambio&a=index");
        exit;
    }
}
