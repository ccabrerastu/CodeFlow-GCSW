<?php
    require_once __DIR__ . '/../model/OrdenCambioModel.php';
    require_once __DIR__ . '/../model/SolicitudCambioModel.php';
    require_once __DIR__ . '/../model/UsuarioModel.php';

    class OrdenCambioControlador {
        private $model;
        private $solModel;
        private $usuarioModel;

        public function __construct() {
            session_start();
            $this->model        = new OrdenCambioModel();
            $this->solModel     = new SolicitudCambioModel();
            $this->usuarioModel = new UsuarioModel();
        }

        public function index() {
            $status  = $_SESSION['status_message'] ?? null;
            unset($_SESSION['status_message']);
            $ordenes = $this->model->obtenerTodasLasOrdenes();
            require __DIR__ . '/../views/ordenCambio/listarOrdenesCambioVista.php';
        }

        public function mostrarFormularioCrear() {
            $id_sc = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$id_sc) {
                $_SESSION['status_message'] = ['type'=>'error','text'=>'ID de Solicitud inválido.'];
                header("Location: index.php?c=SolicitudCambio&a=index");
                exit;
            }

            $formData   = $_SESSION['form_data_orden']   ?? [
                'id_solicitud'       => $id_sc,
                'descripcion_detalle'=> '',
                'id_responsable'     => ''
            ];
            $formErrors = $_SESSION['form_errors_orden'] ?? [];
            unset($_SESSION['form_data_orden'], $_SESSION['form_errors_orden']);

            $solPendientes = $this->solModel->obtenerSolicitudesPorEstado('Aprobada');
            $usuarios      = $this->usuarioModel->obtenerTodosLosUsuarios();

            require __DIR__ . '/../views/ordenCambio/crearOrdenCambioVista.php';
        }

        public function crear() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: index.php?c=OrdenCambio&a=mostrarFormularioCrear");
                exit;
            }

            $id_sc       = filter_input(INPUT_POST, 'id_solicitud',       FILTER_VALIDATE_INT);
            $detalle     = trim($_POST['descripcion_detalle'] ?? '');
            $resp        = filter_input(INPUT_POST, 'id_responsable',     FILTER_VALIDATE_INT);

            $formErrors = [];
            if (!$id_sc)          $formErrors['general']             = "Solicitud inválida.";
            if (empty($detalle))  $formErrors['descripcion_detalle'] = "Detalle obligatorio.";
            if (!$resp)           $formErrors['id_responsable']     = "Responsable requerido.";

            if ($formErrors) {
                $_SESSION['form_data_orden']   = $_POST;
                $_SESSION['form_errors_orden'] = $formErrors;
                header("Location: index.php?c=OrdenCambio&a=mostrarFormularioCrear&id={$id_sc}");
                exit;
            }

            $newId = $this->model->crearOrden($id_sc, $_SESSION['id_usuario'], $detalle);
            $_SESSION['status_message'] = $newId
                ? ['type'=>'success','text'=>"Orden #{$newId} creada exitosamente."]
                : ['type'=>'error','text'=>'Error al crear la orden de cambio.'];

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
            $comentarios = $this->model->obtenerComentariosOC($id_orden);

            $formDataSeg   = $_SESSION['form_data_seguimiento']   ?? [];
            $formErrorsSeg = $_SESSION['form_errors_seguimiento'] ?? [];
            unset($_SESSION['form_data_seguimiento'], $_SESSION['form_errors_seguimiento']);

            require __DIR__ . '/../views/ordenCambio/detalleOrdenCambioVista.php';
        }

        public function registrarSeguimiento() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: index.php?c=OrdenCambio&a=index");
                exit;
            }
            $id_orden    = filter_input(INPUT_POST,'id_orden',FILTER_VALIDATE_INT);
            $nuevoEstado = $_POST['nuevo_estado'] ?? '';
            $comentario  = trim($_POST['comentario'] ?? '');

            $formErrors = [];
            if (!$id_orden)                              $formErrors['general']      = "Orden inválida.";
            if (!in_array($nuevoEstado,['En Proceso','Terminado'])) {
                $formErrors['nuevo_estado'] = "Seleccione un estado válido.";
            }

            if ($formErrors) {
                $_SESSION['form_data_seguimiento']   = $_POST;
                $_SESSION['form_errors_seguimiento'] = $formErrors;
                header("Location: index.php?c=OrdenCambio&a=detalle&id_orden={$id_orden}");
                exit;
            }

            $ok1 = $this->model->actualizarEstadoOC($id_orden, $nuevoEstado);
            $ok2 = true;
            if ($comentario !== '') {
                $ok2 = $this->model->agregarComentarioOC($id_orden, $_SESSION['id_usuario'], $comentario);
            }

            $_SESSION['status_message'] = ($ok1 && $ok2)
                ? ['type'=>'success','text'=>'Seguimiento registrado correctamente.']
                : ['type'=>'error','text'=>'Error al registrar el seguimiento.'];

            header("Location: index.php?c=OrdenCambio&a=detalle&id_orden={$id_orden}");
            exit;
        }

        public function eliminar() {
            $id = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
            $ok = $id ? $this->model->eliminarOrden($id) : false;
            $_SESSION['status_message'] = $ok
                ? ['type'=>'success','text'=>'Orden eliminada.']
                : ['type'=>'error','text'=>'Error al eliminar la orden.'];
            header("Location: index.php?c=OrdenCambio&a=index");
            exit;
        }
    }
?>