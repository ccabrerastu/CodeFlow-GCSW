<?php
require_once __DIR__ . '/../model/ProyectoModel.php';
require_once __DIR__ . '/../model/MetodologiaModel.php';
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../model/EquipoModel.php';


class ProyectoControlador {

    private $proyectoModel;
    private $metodologiaModel;
    private $usuarioModel;
    private $equipoModel; 
    public function __construct() {
        $this->proyectoModel = new ProyectoModel();
        $this->metodologiaModel = new MetodologiaModel();
        $this->usuarioModel = new UsuarioModel();
         $this->equipoModel = new EquipoModel();
    }


    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $statusMessage = $_SESSION['status_message'] ?? null;
        unset($_SESSION['status_message']);

        $proyectos = $this->proyectoModel->obtenerTodosLosProyectos();
        $baseUrl = "/";

        require __DIR__ . '/../views/listarProyectosVista.php';
    }


    public function mostrarFormularioProyecto() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_VALIDATE_INT);
        $proyecto = null;
        $accion = 'crear';
        $tituloPagina = "Crear Nuevo Proyecto";

        if ($id_proyecto) {
            $proyecto = $this->proyectoModel->obtenerProyectoPorId($id_proyecto);
            if ($proyecto) {
                $accion = 'editar';
                $tituloPagina = "Editar Proyecto: " . htmlspecialchars($proyecto['nombre_proyecto']);
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Proyecto no encontrado.'];
                header("Location: index.php?c=Proyecto&a=index");
                exit;
            }
        }

        $metodologias = $this->metodologiaModel->obtenerTodasLasMetodologias();
        $usuarios = $this->usuarioModel->obtenerTodosLosUsuarios();

        $formData = $_SESSION['form_data_proyecto'] ?? ($proyecto ?: ['nombre_proyecto' => '', 'descripcion' => '', 'id_metodologia' => '', 'id_product_owner' => '', 'fecha_inicio_planificada' => '', 'fecha_fin_planificada' => '', 'estado_proyecto' => 'Activo']);
        $formErrors = $_SESSION['form_errors_proyecto'] ?? [];
        unset($_SESSION['form_data_proyecto'], $_SESSION['form_errors_proyecto']);

        $baseUrl = "/";
        require __DIR__ . '/../views/crearEditarProyectoVista.php';
    }

    public function guardarProyecto() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_proyecto = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT);
            $nombre_proyecto = trim($_POST['nombre_proyecto'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $id_metodologia = filter_input(INPUT_POST, 'id_metodologia', FILTER_VALIDATE_INT);
            $id_product_owner = filter_input(INPUT_POST, 'id_product_owner', FILTER_VALIDATE_INT);
            $fecha_inicio_planificada = $_POST['fecha_inicio_planificada'] ?? null;
            $fecha_fin_planificada = $_POST['fecha_fin_planificada'] ?? null;
            $estado_proyecto = $_POST['estado_proyecto'] ?? 'Activo';

            $formErrors = [];
            if (empty($nombre_proyecto)) {
                $formErrors['nombre_proyecto'] = "El nombre del proyecto es obligatorio.";
            }
            if (!$id_metodologia) { 
                $formErrors['id_metodologia'] = "Debe seleccionar una metodología.";
            }

            if (!empty($formErrors)) {
                $_SESSION['form_data_proyecto'] = $_POST;
                $_SESSION['form_errors_proyecto'] = $formErrors;
                $redirectUrl = $id_proyecto ? "index.php?c=Proyecto&a=mostrarFormularioProyecto&id_proyecto=" . $id_proyecto : "index.php?c=Proyecto&a=mostrarFormularioProyecto";
                header("Location: " . $redirectUrl);
                exit;
            }

            $this->proyectoModel->setNombreProyecto($nombre_proyecto);
            $this->proyectoModel->setDescripcion($descripcion);
            $this->proyectoModel->setIdMetodologia($id_metodologia ?: null); // Permite null si no se selecciona
            $this->proyectoModel->setIdProductOwner($id_product_owner ?: null); // Permite null si no se selecciona
            $this->proyectoModel->setFechaInicioPlanificada($fecha_inicio_planificada ?: null);
            $this->proyectoModel->setFechaFinPlanificada($fecha_fin_planificada ?: null);
            $this->proyectoModel->setEstadoProyecto($estado_proyecto);

            if ($id_proyecto) { // Actualizar
                $this->proyectoModel->setIdProyecto($id_proyecto);
                $resultado = $this->proyectoModel->actualizarProyecto();
                $mensajeExito = "Proyecto actualizado exitosamente.";
                $mensajeError = "Error al actualizar el proyecto.";
            } else { // Crear
                $resultado = $this->proyectoModel->crearProyecto();
                $mensajeExito = "Proyecto creado exitosamente.";
                $mensajeError = "Error al crear el proyecto.";
            }

            if ($resultado) {
                $_SESSION['status_message'] = ['type' => 'success', 'text' => $mensajeExito];
                header("Location: index.php?c=Proyecto&a=index");
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => $mensajeError];
                $redirectUrl = $id_proyecto ? "index.php?c=Proyecto&a=mostrarFormularioProyecto&id_proyecto=" . $id_proyecto : "index.php?c=Proyecto&a=mostrarFormularioProyecto";
                header("Location: " . $redirectUrl);
            }
            exit;
        }
        header("Location: index.php?c=Proyecto&a=index");
        exit;
    }

public function planificar() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_VALIDATE_INT);
    if (!$id_proyecto) {
        $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ID de proyecto no válido.'];
        header("Location: index.php?c=Proyecto&a=index");
        exit;
    }

    $proyecto = $this->proyectoModel->obtenerProyectoPorId($id_proyecto);
    if (!$proyecto) {
        $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Proyecto no encontrado.'];
        header("Location: index.php?c=Proyecto&a=index");
        exit;
    }

    $resultadoRoles = $this->equipoModel->obtenerRolesProyecto(); 
    $equipo = $this->equipoModel->obtenerEquipoPorProyecto2($id_proyecto);
   $roles = $resultadoRoles;
    $miembros_equipo = [];
    if (!empty($equipo) && isset($equipo['id_equipo'])) {
        $miembros_equipo = $this->equipoModel->obtenerMiembrosEquipo($equipo['id_equipo']);
    }
    $baseUrl = "/";
    $tituloPagina = "Planificar Proyecto: " . htmlspecialchars($proyecto['nombre_proyecto']);
    $metodologias = $this->metodologiaModel->obtenerTodasLasMetodologias();
    $usuarios = $this->usuarioModel->obtenerTodosLosUsuarios();
    $fases = $this->proyectoModel->obtenerFasesPorProyecto($id_proyecto);

    $formData = $proyecto; 
    $formErrors = [];

    require __DIR__ . '/../views/planificarProyectoVista.php'; 
}
public function agregarECSProyecto() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Verificar permisos

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_proyecto = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT);
            $nombre_ecs = trim($_POST['nombre_ecs'] ?? '');
            $descripcion_ecs = trim($_POST['descripcion_ecs'] ?? '');
            $tipo_ecs = trim($_POST['tipo_ecs'] ?? '');
            $id_actividad_asociada = filter_input(INPUT_POST, 'id_actividad_asociada', FILTER_VALIDATE_INT);

            $formErrors = [];
            if (!$id_proyecto) {
                $formErrors['general_ecs'] = "ID de proyecto no válido.";
            }
            if (empty($nombre_ecs)) {
                $formErrors['nombre_ecs'] = "El nombre del ECS es obligatorio.";
            }

            if (!empty($formErrors)) {
                $_SESSION['form_data_ecs'] = $_POST;
                $_SESSION['form_errors_ecs'] = $formErrors;
                header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=ecs");
                exit;
            }

            $this->ecsModel->setIdProyecto($id_proyecto);
            $this->ecsModel->setNombreEcs($nombre_ecs);
            $this->ecsModel->setDescripcion($descripcion_ecs);
            $this->ecsModel->setTipoEcs($tipo_ecs);
            $this->ecsModel->setVersionActual('1.0'); 
            $this->ecsModel->setEstadoEcs('Definido'); 
            $this->ecsModel->setIdCreador($_SESSION['id_usuario'] ?? null);

            $nuevo_id_ecs = $this->ecsModel->crearECS();

            if ($nuevo_id_ecs) {
                if ($id_actividad_asociada && $id_actividad_asociada > 0) {
                    $this->entregableActividadModel->asociarECSAActividad($id_actividad_asociada, $nuevo_id_ecs);
                }
                $_SESSION['status_message'] = ['type' => 'success', 'text' => 'Elemento de Configuración agregado exitosamente.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al agregar el Elemento de Configuración. Verifique si ya existe un ECS con el mismo nombre en este proyecto.'];
            }
            header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=ecs");
            exit;
        }
        header("Location: index.php?c=Proyecto&a=index");
        exit;
    }
    
    public function eliminarECSProyecto() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Verificar permisos

        $id_ecs = filter_input(INPUT_GET, 'id_ecs', FILTER_VALIDATE_INT);
        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_VALIDATE_INT); 

        if (!$id_ecs || !$id_proyecto) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ID de ECS o proyecto no válido para eliminar.'];
        } else {
            $resultado = $this->ecsModel->eliminarECS($id_ecs);
            if ($resultado) {
                $_SESSION['status_message'] = ['type' => 'success', 'text' => 'Elemento de Configuración eliminado exitosamente.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al eliminar el Elemento de Configuración. Puede estar en uso o no pertenecer a este proyecto.'];
            }
        }
        header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=ecs");
        exit;
    }


}
?>
