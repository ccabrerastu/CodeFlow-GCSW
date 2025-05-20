<?php
require_once __DIR__ . '/../model/ProyectoModel.php';
require_once __DIR__ . '/../model/MetodologiaModel.php';
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../model/EquipoModel.php';
require_once __DIR__ . '/../model/ElementoConfiguracionModel.php';
require_once __DIR__ . '/../model/CronogramaModel.php';
require_once __DIR__ . '/../model/ActividadCronogramaModel.php';
require_once __DIR__ . '/../model/EntregableActividadModel.php';
require_once __DIR__ . '/../model/FasesMetodologiaModel.php';
require_once __DIR__ . '/../model/ECSFaseMetodologiaModel.php';
require_once __DIR__ . '/../model/ECSProyectoModel.php';



class ProyectoControlador {

    private $proyectoModel;
    private $metodologiaModel;
    private $usuarioModel;
    private $equipoModel;
    private $ecsModel;
    private $cronogramaModel;
    private $actividadModel;
    private $entregableActividadModel;
    private $faseMetodologiaModel;
    private $ecsFaseMetodologiaModel;
     private $ecsProyectoModel;

    public function __construct() {
        $this->proyectoModel = new ProyectoModel();
        $this->metodologiaModel = new MetodologiaModel();
        $this->usuarioModel = new UsuarioModel();
        $this->equipoModel = new EquipoModel();
        $this->ecsModel = new ElementoConfiguracionModel();
        $this->cronogramaModel = new CronogramaModel();
        $this->actividadModel = new ActividadCronogramaModel();
        $this->entregableActividadModel = new EntregableActividadModel();
        $this->faseMetodologiaModel = new FasesMetodologiaModel();
        $this->ecsFaseMetodologiaModel = new ECSFaseMetodologiaModel();
        $this->ecsProyectoModel = new ECSProyectoModel();
        
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
            $id_proyecto_form = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT); 
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
                $redirectUrl = $id_proyecto_form ? "index.php?c=Proyecto&a=mostrarFormularioProyecto&id_proyecto=" . $id_proyecto_form : "index.php?c=Proyecto&a=mostrarFormularioProyecto";
                header("Location: " . $redirectUrl);
                exit;
            }

            $this->proyectoModel->setNombreProyecto($nombre_proyecto);
            $this->proyectoModel->setDescripcion($descripcion);
            $this->proyectoModel->setIdMetodologia($id_metodologia ?: null);
            $this->proyectoModel->setIdProductOwner($id_product_owner ?: null);
            $this->proyectoModel->setFechaInicioPlanificada($fecha_inicio_planificada ?: null);
            $this->proyectoModel->setFechaFinPlanificada($fecha_fin_planificada ?: null);
            $this->proyectoModel->setEstadoProyecto($estado_proyecto);

            $id_proyecto_actualizado_o_creado = null;

            if ($id_proyecto_form) { // Actualizar
                $this->proyectoModel->setIdProyecto($id_proyecto_form);
                $resultado = $this->proyectoModel->actualizarProyecto();
                if($resultado) $id_proyecto_actualizado_o_creado = $id_proyecto_form;
                $mensajeExito = "Proyecto actualizado exitosamente.";
                $mensajeError = "Error al actualizar el proyecto."; 
            } else { // Crear
                $id_nuevo  = $this->proyectoModel->crearProyecto();
                if ($id_nuevo ) {
                    $id_proyecto_actualizado_o_creado = $id_nuevo;
                    // Crear cronograma por defecto para el nuevo proyecto
                    //$this->cronogramaModel->setIdProyecto($id_proyecto_nuevo);
                    //$this->cronogramaModel->setDescripcion("Cronograma para el proyecto: " . $nombre_proyecto);
                    //$this->cronogramaModel->crearCronograma(); // Asumiendo que este método existe y funciona
                    $resultado = true; // Asumimos que crearProyecto devuelve el ID o false
                } else {
                    $resultado = false;
                }
                $mensajeExito = "Proyecto creado exitosamente.";
                $mensajeError = "Error al crear el proyecto.";
            }

            if ($resultado ) {
                $_SESSION['status_message'] = ['type' => 'success', 'text' => $mensajeExito];
                header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto_actualizado_o_creado);
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => $mensajeError];
                $redirectUrl = $id_proyecto_form ? "index.php?c=Proyecto&a=mostrarFormularioProyecto&id_proyecto=" . $id_proyecto_form : "index.php?c=Proyecto&a=mostrarFormularioProyecto";
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

        $metodologias = $this->metodologiaModel->obtenerTodasLasMetodologias();
        $usuarios = $this->usuarioModel->obtenerTodosLosUsuarios();
        $equipo = $this->equipoModel->obtenerEquipoPorProyecto($id_proyecto);
        $equipos_existentes = $this->equipoModel->obtenerEquipos();
        $miembros_equipo = [];
        if ($equipo && isset($equipo['id_equipo'])) {
            $miembros_equipo = $this->equipoModel->obtenerMiembrosEquipo($equipo['id_equipo']);
        }
        $roles_proyecto = $this->equipoModel->obtenerRolesProyecto();
        
        $fases_con_ecs_plantilla = [];
        if (isset($proyecto['id_metodologia'])) {
            // Este método ahora obtiene las fases y para cada fase, sus ECS plantilla
            $fases_con_ecs_plantilla = $this->faseMetodologiaModel->obtenerFasesConSusECS($proyecto['id_metodologia']);
        }
        // Obtener los id_ec_fase_met que ya están seleccionados para este proyecto
        $ecs_seleccionados_ids = $this->ecsProyectoModel->obtenerIdsECSeleccionadosPorProyecto($id_proyecto);
        // Obtener los detalles completos de los ECS que están realmente en ECS_Proyecto
        $ecs_del_proyecto_detallados = $this->ecsProyectoModel->obtenerDetallesECSeleccionadosPorProyecto($id_proyecto);

        
        // Cargar cronograma y actividades
        $cronograma = $this->cronogramaModel->obtenerCronogramaPorProyecto($id_proyecto);
        $actividades = [];
        $fases_metodologia_cronograma = [];

        if ($cronograma && isset($cronograma['id_cronograma'])) {
            $actividades = $this->actividadModel->obtenerActividadesPorCronograma($cronograma['id_cronograma']); // Necesitarás este método
        }
        if (isset($proyecto['id_metodologia'])) {
            $fases_metodologia_cronograma = $this->faseMetodologiaModel->obtenerFasesPorMetodologia($proyecto['id_metodologia']);
        }


        $statusMessage = $_SESSION['status_message'] ?? null;
        unset($_SESSION['status_message']);
        
        $baseUrl = "/";
        $tituloPagina = "Planificar Proyecto: " . htmlspecialchars($proyecto['nombre_proyecto']);
        
        $formDataEquipo = $_SESSION['form_data_equipo'] ?? [];
        $formErrorsEquipo = $_SESSION['form_errors_equipo'] ?? [];
        unset($_SESSION['form_data_equipo'], $_SESSION['form_errors_equipo']);

        $formDataECS = $_SESSION['form_data_ecs'] ?? ['nombre_ecs' => '', 'descripcion_ecs' => '', 'tipo_ecs' => '', 'id_actividad_asociada' => ''];
        $formErrorsECS = $_SESSION['form_errors_ecs'] ?? [];
        unset($_SESSION['form_data_ecs'], $_SESSION['form_errors_ecs']);

        $formDataActividad = $_SESSION['form_data_actividad'] ?? ['nombre_actividad' => '', 'descripcion_actividad' => '', 'id_fase_metodologia' => '', 'fecha_inicio_planificada' => '', 'fecha_fin_planificada' => '', 'id_responsable' => '', 'id_ecs_entregable' => ''];
        $formErrorsActividad = $_SESSION['form_errors_actividad'] ?? [];
        unset($_SESSION['form_data_actividad'], $_SESSION['form_errors_actividad']);


        $fases_metodologia = $this->faseMetodologiaModel->obtenerFasesPorMetodologia($proyecto['id_metodologia']);

        require __DIR__ . '/../views/planificarProyectoVista.php';
    }

    public function crearCronogramaParaProyecto() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Verificar permisos (PO/JP del proyecto)

        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_VALIDATE_INT);
        if (!$id_proyecto) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ID de proyecto no válido para crear cronograma.'];
            header("Location: index.php?c=Proyecto&a=index");
            exit;
        }

        $proyecto = $this->proyectoModel->obtenerProyectoPorId($id_proyecto);
        if (!$proyecto) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Proyecto no encontrado.'];
            header("Location: index.php?c=Proyecto&a=index");
            exit;
        }

        // Verificar si ya existe un cronograma
        $cronogramaExistente = $this->cronogramaModel->obtenerCronogramaPorProyecto($id_proyecto);
        if ($cronogramaExistente) {
            $_SESSION['status_message'] = ['type' => 'info', 'text' => 'El proyecto ya tiene un cronograma.'];
        } else {
            $this->cronogramaModel->setIdProyecto($id_proyecto);
            $this->cronogramaModel->setDescripcion("Cronograma para el proyecto: " . $proyecto['nombre_proyecto']);
            $resultado = $this->cronogramaModel->crearCronograma();

            if ($resultado) {
                $_SESSION['status_message'] = ['type' => 'success', 'text' => 'Cronograma creado exitosamente.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al crear el cronograma.'];
            }
        }
        header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=cronograma");
        exit;
    }

    public function guardarSeleccionECSProyecto() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Verificar permisos

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_proyecto = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT);
            // Los checkboxes seleccionados vendrán como un array
            $ids_ec_fase_met_seleccionados = $_POST['elementos_seleccionados'] ?? []; 

            if (!$id_proyecto) {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ID de proyecto no válido.'];
                // Podríamos redirigir a una página de error o a la lista de proyectos
                header("Location: index.php?c=Proyecto&a=index");
                exit;
            }

            $resultado = $this->ecsProyectoModel->guardarSeleccionECS($id_proyecto, $ids_ec_fase_met_seleccionados);

            if ($resultado) {
                $_SESSION['status_message'] = ['type' => 'success', 'text' => 'Selección de ECS del proyecto guardada exitosamente.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al guardar la selección de ECS del proyecto.'];
            }
            header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=ecs");
            exit;
        }
        // Si no es POST, redirigir
        $id_proyecto_get = filter_input(INPUT_GET, 'id_proyecto', FILTER_VALIDATE_INT);
        header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . ($id_proyecto_get ?: '') . "&tab=ecs");
        exit;
    }

    public function agregarECSProyecto() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Verificar permisos

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_proyecto = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT);
            $nombre_ecs = trim($_POST['nombre_ecs'] ?? '');
            $descripcion_ecs = trim($_POST['descripcion_ecs'] ?? '');
            $tipo_ecs = trim($_POST['tipo_ecs'] ?? '');
            $id_fase_metodologia = filter_input(INPUT_POST, 'id_fase_metodologia', FILTER_VALIDATE_INT);


            $formErrors = [];
            if (!$id_proyecto) {
                $formErrors['general_ecs'] = "ID de proyecto no válido.";
            }
            if (empty($nombre_ecs)) {
                $formErrors['nombre_ecs'] = "El nombre del ECS es obligatorio.";
            }
            // Aquí podrías añadir más validaciones, como verificar si ya existe un ECS con ese nombre

            if (!empty($formErrors)) {
                $_SESSION['form_data_ecs'] = $_POST;
                $_SESSION['form_errors_ecs'] = $formErrors;
                header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=ecs");
                exit;
            }

            // 1. Crear el ECS en la tabla ElementosConfiguracion (catálogo general)
            $this->ecsModel->setNombreEcs($nombre_ecs);
            $this->ecsModel->setDescripcion($descripcion_ecs);
            $this->ecsModel->setTipoEcs($tipo_ecs);
            $this->ecsModel->setVersionActual('1.0'); 
            $this->ecsModel->setEstadoEcs('Definido'); // O 'Personalizado'
            // $this->ecsModel->setIdCreador($_SESSION['id_usuario'] ?? null); // id_creador ya no está en ElementosConfiguracion
            
            $nuevo_id_ecs_catalogo = $this->ecsModel->crearECS();

            if ($nuevo_id_ecs_catalogo) {
                // 2. Crear una entrada en ECS_FaseMetodologia para este ECS personalizado (id_fase_metodologia = NULL)
                $id_ec_fase_met = $this->ecsFaseMetodologiaModel->asociarECSAFase($nuevo_id_ecs_catalogo, $id_fase_metodologia, "ECS personalizado para proyecto ID: " . $id_proyecto);

                if ($id_ec_fase_met) {
                    // 3. Crear la entrada en ECS_Proyecto
                    // Usamos el método guardarSeleccionECS, pasándole solo este nuevo id_ec_fase_met
                    // Primero obtenemos los ya seleccionados para no borrarlos
                    $ecs_actualmente_seleccionados = $this->ecsProyectoModel->obtenerIdsECSeleccionadosPorProyecto($id_proyecto);
                    $todos_los_seleccionados = array_merge($ecs_actualmente_seleccionados, [$id_ec_fase_met]);
                    $todos_los_seleccionados = array_unique($todos_los_seleccionados); // Evitar duplicados

                    $resultado_proyecto_ecs = $this->ecsProyectoModel->guardarSeleccionECS($id_proyecto, $todos_los_seleccionados);

                    if ($resultado_proyecto_ecs) {
                        $_SESSION['status_message'] = ['type' => 'success', 'text' => 'ECS personalizado agregado y asociado al proyecto exitosamente.'];
                    } else {
                        $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ECS creado en catálogo, pero hubo un error al asociarlo al proyecto.'];
                        // Aquí podrías considerar eliminar el ECS del catálogo si la asociación falla, o manejarlo de otra forma.
                    }
                } else {
                    $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ECS creado en catálogo, pero hubo un error al crear la entrada de fase/metodología.'];
                }
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al agregar el Elemento de Configuración al catálogo. Verifique si ya existe.'];
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

    // --- Métodos para gestionar Actividades del Cronograma ---
    public function agregarActividadCronograma() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Verificar permisos (PO/JP del proyecto)

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_proyecto = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT);
            $id_cronograma = filter_input(INPUT_POST, 'id_cronograma', FILTER_VALIDATE_INT);
            $nombre_actividad = trim($_POST['nombre_actividad'] ?? '');
            $descripcion_actividad = trim($_POST['descripcion_actividad'] ?? '');
            $id_fase_metodologia = filter_input(INPUT_POST, 'id_fase_metodologia', FILTER_VALIDATE_INT);
            $fecha_inicio_plan = $_POST['fecha_inicio_planificada'] ?? null;
            $fecha_fin_plan = $_POST['fecha_fin_planificada'] ?? null;
            $id_responsable = filter_input(INPUT_POST, 'id_responsable', FILTER_VALIDATE_INT);
            $id_ecs_entregable = filter_input(INPUT_POST, 'id_ecs_entregable', FILTER_VALIDATE_INT); // Para asociar un ECS

            $formErrors = [];
            if (!$id_proyecto || !$id_cronograma) {
                $formErrors['general_actividad'] = "ID de proyecto o cronograma no válido.";
            }
            if (empty($nombre_actividad)) {
                $formErrors['nombre_actividad'] = "El nombre de la actividad es obligatorio.";
            }
            // Validar fechas
            if (!empty($fecha_inicio_plan) && !empty($fecha_fin_plan) && $fecha_fin_plan < $fecha_inicio_plan) {
                $formErrors['fecha_fin_planificada'] = "La fecha de fin no puede ser anterior a la fecha de inicio.";
            }

            if (!empty($formErrors)) {
                $_SESSION['form_data_actividad'] = $_POST;
                $_SESSION['form_errors_actividad'] = $formErrors;
                header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=cronograma");
                exit;
            }

            $this->actividadModel->setIdCronograma($id_cronograma);
            $this->actividadModel->setNombreActividad($nombre_actividad);
            $this->actividadModel->setDescripcion($descripcion_actividad);
            $this->actividadModel->setIdFaseMetodologia($id_fase_metodologia ?: null);
            $this->actividadModel->setFechaInicioPlanificada($fecha_inicio_plan ?: null);
            $this->actividadModel->setFechaFinPlanificada($fecha_fin_plan ?: null);
            $this->actividadModel->setIdResponsable($id_responsable ?: null);
            // estado_actividad se puede poner por defecto en el modelo o aquí

            $nueva_id_actividad = $this->actividadModel->crearActividad();

            if ($nueva_id_actividad) {
                if ($id_ecs_entregable && $id_ecs_entregable > 0) {
                    $this->entregableActividadModel->asociarECSAActividad($nueva_id_actividad, $id_ecs_entregable);
                }
                $_SESSION['status_message'] = ['type' => 'success', 'text' => 'Actividad agregada al cronograma exitosamente.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al agregar la actividad.'];
            }
            header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=cronograma");
            exit;
        }
        header("Location: index.php?c=Proyecto&a=index");
        exit;
    }

    public function mostrarFormularioEditarActividad() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Verificar permisos

        $id_actividad = filter_input(INPUT_GET, 'id_actividad', FILTER_VALIDATE_INT);
        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_VALIDATE_INT); // Para contexto y redirección
        $cronograma = $this->cronogramaModel->obtenerCronogramaPorProyecto($id_proyecto); // Necesario para id_cronograma en el form


        if (!$id_actividad || !$id_proyecto) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ID de actividad o proyecto no válido.'];
            header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=cronograma");
            exit;
        }

        $actividad = $this->actividadModel->obtenerActividadPorId($id_actividad); // Necesitarás este método en ActividadCronogramaModel
        $proyecto = $this->proyectoModel->obtenerProyectoPorId($id_proyecto); // Para el nombre del proyecto y id_metodologia
        $ecs_proyecto = $this->ecsProyectoModel->obtenerDetallesECSeleccionadosPorProyecto($id_proyecto);
        if (!$actividad || !$proyecto) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Actividad o proyecto no encontrado.'];
            header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=cronograma");
            exit;
        }
        

        $fases_metodologia_cronograma = [];
        if (isset($proyecto['id_metodologia'])) {
            $fases_metodologia_cronograma = $this->faseMetodologiaModel->obtenerFasesPorMetodologia($proyecto['id_metodologia']);
        }
        $usuarios_equipo = $this->equipoModel->obtenerMiembrosParaSelect($id_proyecto); // Necesitarás un método que devuelva id_usuario y nombre_completo de los miembros del proyecto
        $ecs_proyecto = $this->ecsModel->obtenerECS_PorProyecto($id_proyecto); // Para el selector de ECS entregable

        // Obtener el ECS actualmente asociado a la actividad para preseleccionar
        $entregable_actual = $this->entregableActividadModel->obtenerECSAsociadosAActividad($id_actividad); // Asume que este método devuelve un solo ECS o null
        $actividad['id_ecs_entregable'] = $entregable_actual ? $entregable_actual['id_ecs'] : null;


        $formData = $_SESSION['form_data_actividad_edit'] ?? $actividad;
        $formErrors = $_SESSION['form_errors_actividad_edit'] ?? [];
        unset($_SESSION['form_data_actividad_edit'], $_SESSION['form_errors_actividad_edit']);

        $baseUrl = "/";
        $accion_form = "actualizarActividadCronograma";
        $titulo_form = "Editar Actividad del Cronograma";
        require __DIR__ . '/../views/proyectos/crearEditarActividadVista.php'; // Necesitarás crear esta vista
    }

    public function actualizarActividadCronograma() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Verificar permisos

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_actividad = filter_input(INPUT_POST, 'id_actividad', FILTER_VALIDATE_INT);
            $id_proyecto = filter_input(INPUT_POST, 'id_proyecto_redirect', FILTER_VALIDATE_INT); // Para redirección
            $nombre_actividad = trim($_POST['nombre_actividad'] ?? '');
            $descripcion_actividad = trim($_POST['descripcion_actividad'] ?? '');
            $id_fase_metodologia = filter_input(INPUT_POST, 'id_fase_metodologia', FILTER_VALIDATE_INT);
            $fecha_inicio_plan = $_POST['fecha_inicio_planificada'] ?? null;
            $fecha_fin_plan = $_POST['fecha_fin_planificada'] ?? null;
            $estado_actividad = $_POST['estado_actividad'] ?? '';
            $id_responsable = filter_input(INPUT_POST, 'id_responsable', FILTER_VALIDATE_INT);
            $id_ecs_entregable_nuevo = filter_input(INPUT_POST, 'id_ecs_entregable', FILTER_VALIDATE_INT);
            $fecha_entrega_real = $_POST['fecha_entrega_real'] ?? null;


            $formErrors = [];
            if (!$id_actividad || !$id_proyecto) {
                $formErrors['general_actividad'] = "ID de actividad o proyecto no válido.";
            }
            if (empty($nombre_actividad)) {
                $formErrors['nombre_actividad'] = "El nombre de la actividad es obligatorio.";
            }
            if (!empty($fecha_inicio_plan) && !empty($fecha_fin_plan) && $fecha_fin_plan < $fecha_inicio_plan) {
                $formErrors['fecha_fin_planificada'] = "La fecha de fin no puede ser anterior a la fecha de inicio.";
            }
             if (empty($estado_actividad)) {
                $formErrors['estado_actividad'] = "El estado de la actividad es obligatorio.";
            }


            if (!empty($formErrors)) {
                $_SESSION['form_data_actividad_edit'] = $_POST;
                $_SESSION['form_errors_actividad_edit'] = $formErrors;
                header("Location: index.php?c=Proyecto&a=mostrarFormularioEditarActividad&id_actividad=" . $id_actividad . "&id_proyecto=" . $id_proyecto);
                exit;
            }

            $this->actividadModel->setIdActividad($id_actividad);
            // El id_cronograma se mantiene, no se actualiza directamente aquí
            $this->actividadModel->setNombreActividad($nombre_actividad);
            $this->actividadModel->setDescripcion($descripcion_actividad);
            $this->actividadModel->setIdFaseMetodologia($id_fase_metodologia ?: null);
            $this->actividadModel->setFechaInicioPlanificada($fecha_inicio_plan ?: null);
            $this->actividadModel->setFechaFinPlanificada($fecha_fin_plan ?: null);
            $this->actividadModel->setEstadoActividad($estado_actividad);
            $this->actividadModel->setIdResponsable($id_responsable ?: null);
            $this->actividadModel->setFechaEntregaReal($fecha_entrega_real ?: null);


            $resultado = $this->actividadModel->actualizarActividad(); // Necesitarás este método en ActividadCronogramaModel

            if ($resultado) {
                // Actualizar la asociación del ECS entregable
                // Primero, desasociar el antiguo si existía y es diferente del nuevo
                $entregable_actual = $this->entregableActividadModel->obtenerECSAsociadosAActividad($id_actividad);
                if ($entregable_actual && $entregable_actual['id_ecs'] != $id_ecs_entregable_nuevo) {
                    $this->entregableActividadModel->desasociarECSDeActividad($id_actividad, $entregable_actual['id_ecs']);
                }
                // Asociar el nuevo si se seleccionó uno y es diferente (o no había antes)
                if ($id_ecs_entregable_nuevo && $id_ecs_entregable_nuevo > 0 && (!$entregable_actual || $entregable_actual['id_ecs'] != $id_ecs_entregable_nuevo)) {
                    $this->entregableActividadModel->asociarECSAActividad($id_actividad, $id_ecs_entregable_nuevo);
                } elseif (empty($id_ecs_entregable_nuevo) && $entregable_actual) { // Si se deseleccionó
                     $this->entregableActividadModel->desasociarECSDeActividad($id_actividad, $entregable_actual['id_ecs']);
                }

                $_SESSION['status_message'] = ['type' => 'success', 'text' => 'Actividad actualizada exitosamente.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al actualizar la actividad.'];
            }
            header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=cronograma");
            exit;
        }
        // Si no es POST, redirigir
        $id_proyecto_get = filter_input(INPUT_GET, 'id_proyecto', FILTER_VALIDATE_INT) ?? filter_input(INPUT_POST, 'id_proyecto_redirect', FILTER_VALIDATE_INT);
        header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto_get . "&tab=cronograma");
        exit;
    }
    
    public function eliminarActividadCronograma() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Verificar permisos

        $id_actividad = filter_input(INPUT_GET, 'id_actividad', FILTER_VALIDATE_INT);
        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_VALIDATE_INT); // Para la redirección

        if (!$id_actividad || !$id_proyecto) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ID de actividad o proyecto no válido.'];
        } else {
            // Antes de eliminar, podrías desasociar ECS si es necesario o manejarlo con FK
            $resultado = $this->actividadModel->eliminarActividad($id_actividad); // Necesitarás este método
            if ($resultado) {
                $_SESSION['status_message'] = ['type' => 'success', 'text' => 'Actividad eliminada exitosamente.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al eliminar la actividad.'];
            }
        }
        header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $id_proyecto . "&tab=cronograma");
        exit;
    }

}
?>
