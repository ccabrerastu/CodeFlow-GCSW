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
            'id_proyecto'   => '',
            'prioridad'     => '',
            'tipo_cambio'   => '',
            'justificacion' => '',
            'titulo'        => '',
            'descripcion'   => ''
        ];
        unset($_SESSION['form_data_solicitud'], $_SESSION['form_errors_solicitud']);

        if (!empty($formData['prioridad']) && !empty($formData['tipo_cambio'])) {
            $imp = $this->model->calcularImpacto($formData['prioridad'], $formData['tipo_cambio']);
            $formData['impacto']     = $imp['nivel'];
            $formData['impacto_est'] = $imp['porcentaje'];
        } else {
            $formData['impacto']     = '';
            $formData['impacto_est'] = '';
        }

        $proyectos = $this->proyectoModel->obtenerTodosLosProyectos();
        require __DIR__ . '/../views/solicitudCambio/crearEditarSolicitudVista.php';
    }

    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=SolicitudCambio&a=mostrarFormularioCrear");
            exit;
        }

        $id_proyecto   = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT);
        $id_solicitante= $_SESSION['id_usuario'] ?? null;
        $prioridad     = $_POST['prioridad']     ?? '';
        $tipo_cambio   = $_POST['tipo_cambio']   ?? '';
        $justificacion = trim($_POST['justificacion'] ?? '');
        $titulo        = trim($_POST['titulo']        ?? '');
        $descripcion   = trim($_POST['descripcion']   ?? '');

        $formErrors = [];
        if (!$id_proyecto)                                                      $formErrors['id_proyecto']   = "Debe seleccionar un proyecto.";
        if (!in_array($prioridad,     ['ALTA','MEDIA','BAJA'], true))           $formErrors['prioridad']     = "Seleccione una prioridad válida.";
        if (!in_array($tipo_cambio,   ['CORRECCION','MEJORA','NUEVA_FUNCIONALIDAD'], true)) $formErrors['tipo_cambio'] = "Seleccione un tipo válido.";
        if (empty($justificacion))                                              $formErrors['justificacion'] = "La justificación es obligatoria.";
        if (empty($titulo))                                                     $formErrors['titulo']        = "El título es obligatorio.";

        if ($formErrors) {
            $_SESSION['form_data_solicitud']   = $_POST;
            $_SESSION['form_errors_solicitud'] = $formErrors;
            header("Location: index.php?c=SolicitudCambio&a=mostrarFormularioCrear");
            exit;
        }

        $newId = $this->model->crearSolicitud(
            $id_proyecto,
            $id_solicitante,
            $prioridad,
            $tipo_cambio,
            $justificacion,
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
        unset($_SESSION['form_data_solicitud'], $_SESSION['form_errors_solicitud']);

        $proyectos = $this->proyectoModel->obtenerTodosLosProyectos();
        require __DIR__ . '/../views/solicitudCambio/crearEditarSolicitudVista.php';
    }

    public function editar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=SolicitudCambio&a=index");
            exit;
        }

        $id_solicitud  = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
        $id_proyecto   = filter_input(INPUT_POST, 'id_proyecto',  FILTER_VALIDATE_INT);
        $prioridad     = $_POST['prioridad']     ?? '';
        $tipo_cambio   = $_POST['tipo_cambio']   ?? '';
        $justificacion = trim($_POST['justificacion'] ?? '');
        $titulo        = trim($_POST['titulo']       ?? '');
        $descripcion   = trim($_POST['descripcion']  ?? '');

        $formErrors = [];
        if (!$id_solicitud)                                                     $formErrors['general']        = "ID inválido.";
        if (!$id_proyecto)                                                      $formErrors['id_proyecto']    = "Debe seleccionar un proyecto.";
        if (!in_array($prioridad,   ['ALTA','MEDIA','BAJA'], true))             $formErrors['prioridad']      = "Seleccione una prioridad válida.";
        if (!in_array($tipo_cambio, ['CORRECCION','MEJORA','NUEVA_FUNCIONALIDAD'], true)) $formErrors['tipo_cambio'] = "Seleccione un tipo válido.";
        if (empty($justificacion))                                              $formErrors['justificacion']  = "La justificación es obligatoria.";
        if (empty($titulo))                                                     $formErrors['titulo']         = "El título es obligatorio.";

        if ($formErrors) {
            $_SESSION['form_data_solicitud']   = $_POST;
            $_SESSION['form_errors_solicitud'] = $formErrors;
            header("Location: index.php?c=SolicitudCambio&a=mostrarFormularioEditar&id_solicitud={$id_solicitud}");
            exit;
        }

        $ok = $this->model->actualizarSolicitud(
            $id_solicitud,
            $prioridad,
            $tipo_cambio,
            $justificacion,
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

        $id            = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
        $estado        = $_POST['estado_sc']       ?? '';
        $decision_text = trim($_POST['decision_final'] ?? '');

        $errors = [];
        if (!$id) {
            $errors['general'] = "ID de solicitud inválido.";
        }
        if (!in_array($estado, ['Aprobada','Rechazada'], true)) {
            $errors['estado_sc'] = "Seleccione Aprobada o Rechazada.";
        }
        if ($decision_text === '') {
            $errors['decision_final'] = "El comentario justificativo es obligatorio.";
        }

        if ($errors) {
            $_SESSION['form_errors_solicitud'] = $errors;
            $_SESSION['form_data_solicitud']   = [
                'estado_sc'      => $estado,
                'decision_final' => $decision_text
            ];
            header("Location: index.php?c=SolicitudCambio&a=detalle&id_solicitud={$id}");
            exit;
        }

        $ok = $this->model->actualizarDecisionFinal($id, $estado, $decision_text);

        $_SESSION['status_message'] = $ok
            ? ['type'=>'success','text'=>"Decisión '{$estado}' registrada correctamente."]
            : ['type'=>'error','text'=>'Error al guardar la decisión.'];

        header("Location: index.php?c=SolicitudCambio&a=detalle&id_solicitud={$id}");
        exit;
    }

    public function descargarArchivo()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            die("Adjunto inválido.");
        }
        $file = $this->model->obtenerArchivoPorId($id);
        if (!$file) {
            die("Archivo no encontrado.");
        }
        $ruta = $_SERVER['DOCUMENT_ROOT'] . $file['ruta_archivo'];
        if (!is_readable($ruta)) {
            die("No se puede leer el archivo.");
        }

        header('Content-Type: ' . $file['tipo_archivo']);
        header('Content-Disposition: attachment; filename="' . basename($file['nombre_archivo']) . '"');
        readfile($ruta);
        exit;
    }
}