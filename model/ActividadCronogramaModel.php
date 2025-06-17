<?php
require_once __DIR__ . '/../config/database.php';

class ActividadCronogramaModel {
    private $id_actividad;
    private $id_cronograma;
    private $id_esc;
    private $id_fase_metodologia;
    private $nombre_actividad;
    private $descripcion;
    private $fecha_inicio_planificada;
    private $fecha_fin_planificada;
    private $fecha_entrega_real;
    private $estado_actividad;
    private $id_responsable;

    private $conexion;

    public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en ActividadCronogramaModel: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }

    // --- Getters y Setters ---
    public function getIdActividad() { return $this->id_actividad; }
    public function setIdActividad($id_actividad) { $this->id_actividad = $id_actividad; }

    public function getIdCronograma() { return $this->id_cronograma; }
    public function setIdCronograma($id_cronograma) { $this->id_cronograma = $id_cronograma; }

    public function setIdEsc($id_esc) {  $this->id_esc = $id_esc;}


    public function getIdFaseMetodologia() { return $this->id_fase_metodologia; }
    public function setIdFaseMetodologia($id_fase_metodologia) { $this->id_fase_metodologia = $id_fase_metodologia; }

    public function getNombreActividad() { return $this->nombre_actividad; }
    public function setNombreActividad($nombre_actividad) { $this->nombre_actividad = $nombre_actividad; }

    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }

    public function getFechaInicioPlanificada() { return $this->fecha_inicio_planificada; }
    public function setFechaInicioPlanificada($fecha) { $this->fecha_inicio_planificada = $fecha; }

    public function getFechaFinPlanificada() { return $this->fecha_fin_planificada; }
    public function setFechaFinPlanificada($fecha) { $this->fecha_fin_planificada = $fecha; }

    public function getFechaEntregaReal() { return $this->fecha_entrega_real; }
    public function setFechaEntregaReal($fecha) { $this->fecha_entrega_real = $fecha; }

    public function getEstadoActividad() { return $this->estado_actividad; }
    public function setEstadoActividad($estado) { $this->estado_actividad = $estado; }

    public function getIdResponsable() { return $this->id_responsable; }
    public function setIdResponsable($id_responsable) { $this->id_responsable = $id_responsable; }


    public function crearActividad() {
        if ($this->conexion === null) {
            error_log("ActividadCronogramaModel: No hay conexión a la base de datos.");
            return false;
        }
        $sql = "INSERT INTO ActividadesCronograma 
                    (id_cronograma, id_fase_metodologia, nombre_actividad, descripcion, 
                     fecha_inicio_planificada, fecha_fin_planificada, estado_actividad, id_responsable, id_esc) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (crearActividad): " . $this->conexion->error);
            return false;
        }

        $estado = $this->estado_actividad ?? 'Pendiente';

        

        $stmt->bind_param("iisssssii",
            $this->id_cronograma,
            $this->id_fase_metodologia,
            $this->nombre_actividad,
            $this->descripcion,
            $this->fecha_inicio_planificada,
            $this->fecha_fin_planificada,
            $estado,
            $this->id_responsable,
            $this->id_esc
        );

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $stmt->close();
            return $new_id;
        } else {
            error_log("Error al ejecutar la consulta (crearActividad): " . $stmt->error);
            $stmt->close();
            return false;
        }
    }


    public function obtenerActividadesPorCronograma($id_cronograma) {
        if ($this->conexion === null) return [];
        
        $sql = "SELECT ac.*, fm.nombre_fase, u.nombre_completo as nombre_responsable, ac.nombre_actividad as nombre_actividad
                FROM ActividadesCronograma ac
                LEFT JOIN FasesMetodologia fm ON ac.id_fase_metodologia = fm.id_fase_metodologia
                LEFT JOIN Usuarios u ON ac.id_responsable = u.id_usuario
                WHERE ac.id_cronograma = ? 
                ORDER BY ac.fecha_inicio_planificada ASC, ac.nombre_actividad ASC";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerActividadesPorCronograma: " . $this->conexion->error);
            return [];
        }
        $stmt->bind_param("i", $id_cronograma);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $actividades = [];
        while ($fila = $resultado->fetch_assoc()) {
            $actividades[] = $fila;
        }
        $stmt->close();
        return $actividades;
    }


    public function obtenerActividadesPorProyecto($id_proyecto) {
        if ($this->conexion === null) return [];
        

        $sql_cronograma = "SELECT id_cronograma FROM Cronogramas WHERE id_proyecto = ? LIMIT 1";
        $stmt_cronograma = $this->conexion->prepare($sql_cronograma);
        if (!$stmt_cronograma) {
            error_log("Error prepare obtenerCronogramaId en ActividadModel: " . $this->conexion->error);
            return [];
        }
        $stmt_cronograma->bind_param("i", $id_proyecto);
        $stmt_cronograma->execute();
        $resultado_cronograma = $stmt_cronograma->get_result();
        $cronograma_data = $resultado_cronograma->fetch_assoc();
        $stmt_cronograma->close();

        if (!$cronograma_data || !isset($cronograma_data['id_cronograma'])) {
            return [];
        }
        $id_cronograma = $cronograma_data['id_cronograma'];

        return $this->obtenerActividadesPorCronograma($id_cronograma);
    }



    public function obtenerActividadPorId($id_actividad) {
        if ($this->conexion === null) return null;
        
        $sql = "SELECT 
                    ac.*, 
                    p.id_proyecto, -- <<< AÑADIDO ESTO
                    p.nombre_proyecto,
                    fm.nombre_fase, 
                    u.nombre_completo as nombre_responsable
                FROM ActividadesCronograma ac
                JOIN Cronogramas c ON ac.id_cronograma = c.id_cronograma
                JOIN Proyectos p ON c.id_proyecto = p.id_proyecto
                LEFT JOIN FasesMetodologia fm ON ac.id_fase_metodologia = fm.id_fase_metodologia
                LEFT JOIN Usuarios u ON ac.id_responsable = u.id_usuario
                WHERE ac.id_actividad = ?";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerActividadPorId: " . $this->conexion->error);
            return null;
        }
        $stmt->bind_param("i", $id_actividad);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $actividad = $resultado->fetch_assoc();
        $stmt->close();
        return $actividad;
    }



    
    public function actualizarActividad() {
        if ($this->conexion === null || $this->id_actividad === null) {
            error_log("ActividadCronogramaModel: No hay conexión o ID de actividad no especificado.");
            return false;
        }
        $sql = "UPDATE ActividadesCronograma SET 
                    id_fase_metodologia = ?, 
                    nombre_actividad = ?, 
                    descripcion = ?, 
                    fecha_inicio_planificada = ?, 
                    fecha_fin_planificada = ?,
                    fecha_entrega_real = ?,
                    estado_actividad = ?, 
                    id_responsable = ?
                WHERE id_actividad = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (actualizarActividad): " . $this->conexion->error);
            return false;
        }
        
        $stmt->bind_param("issssssii",
            $this->id_fase_metodologia,
            $this->nombre_actividad,
            $this->descripcion,
            $this->fecha_inicio_planificada,
            $this->fecha_fin_planificada,
            $this->fecha_entrega_real,
            $this->estado_actividad,
            $this->id_responsable,
            $this->id_actividad
        );



        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (actualizarActividad): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }


    public function eliminarActividad($id_actividad) {
        if ($this->conexion === null) return false;
        $sql = "DELETE FROM ActividadesCronograma WHERE id_actividad = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare eliminarActividad: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("i", $id_actividad);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (eliminarActividad): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    public function obtenerActividadesPorResponsable($id_usuario) {
        if ($this->conexion === null) return [];
        
        $sql = "SELECT 
                    ac.*, 
                    p.nombre_proyecto,
                    c.descripcion as nombre_cronograma,
                    fm.nombre_fase
                FROM ActividadesCronograma ac
                JOIN Cronogramas c ON ac.id_cronograma = c.id_cronograma
                JOIN Proyectos p ON c.id_proyecto = p.id_proyecto
                LEFT JOIN FasesMetodologia fm ON ac.id_fase_metodologia = fm.id_fase_metodologia
                WHERE ac.id_responsable = ?
                ORDER BY p.nombre_proyecto ASC, ac.fecha_inicio_planificada ASC";

        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerActividadesPorResponsable: " . $this->conexion->error);
            return [];
        }
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $actividades = [];
        while ($fila = $resultado->fetch_assoc()) {
            $actividades[] = $fila;
        }
        $stmt->close();
        return $actividades;
    }

    public function actualizarEstadoActividad($id_actividad, $nuevo_estado) {
        if ($this->conexion === null) return false;
        
        $sql = "UPDATE ActividadesCronograma SET estado_actividad = ? WHERE id_actividad = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare actualizarEstadoActividad: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("si", $nuevo_estado, $id_actividad);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }



    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
?>
