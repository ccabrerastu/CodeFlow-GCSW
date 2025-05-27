<?php

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

        if ($newId && !empty($_FILES['archivos'])) {
        foreach ($_FILES['archivos']['error'] as $i => $error) {
            if ($error === UPLOAD_ERR_OK) {
            $tmp  = $_FILES['archivos']['tmp_name'][$i];
            $name = basename($_FILES['archivos']['name'][$i]);
            $dest = __DIR__ . "/../public/uploads/sc_$newId/$name";
            if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
            if (move_uploaded_file($tmp, $dest)) {
                $this->model->guardarArchivo($newId, $name, $_FILES['archivos']['type'][$i], "/uploads/sc_$newId/$name");
            }
            }
        }
        }

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

        $archivos = $this->model->obtenerArchivosPorSolicitud($id_solicitud);
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

    public function registrarAnalisis()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=SolicitudCambio&a=index");
            exit;
        }

        $id   = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
        $text = trim($_POST['analisis_impacto'] ?? '');

        if (!$id || $text === '') {
            $_SESSION['status_message'] = ['type'=>'error','text'=>'Debe completar el análisis de impacto.'];
        } else {
            $ok = $this->model->actualizarAnalisisImpacto($id, $text);
            $_SESSION['status_message'] = $ok
                ? ['type'=>'success','text'=>'Análisis de impacto guardado.']
                : ['type'=>'error','text'=>'Error al guardar el análisis.'];
        }

        header("Location: index.php?c=SolicitudCambio&a=detalle&id_solicitud={$id}");
        exit;
    }

    public function registrarDecision()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=SolicitudCambio&a=index");
            exit;
        }

        $id   = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
        $text = trim($_POST['decision_final'] ?? '');

        if (!$id || $text === '') {
            $_SESSION['status_message'] = ['type'=>'error','text'=>'Debe ingresar la decisión final.'];
        } else {
            $ok = $this->model->actualizarDecisionFinal($id, $text);
            $_SESSION['status_message'] = $ok
                ? ['type'=>'success','text'=>'Decisión final registrada.']
                : ['type'=>'error','text'=>'Error al guardar la decisión.'];
        }

        header("Location: index.php?c=SolicitudCambio&a=detalle&id_solicitud={$id}");
        exit;
    }
}
