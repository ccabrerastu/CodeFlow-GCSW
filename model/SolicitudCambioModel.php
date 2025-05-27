<?php
require_once __DIR__ . '/../config/database.php';

class SolicitudCambioModel {
    private $db;

    public function __construct() {
        try {
            $this->db = (new Conexion())->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en SolicitudCambioModel: " . $e->getMessage());
            throw $e;
        }
    }

   
    public function crearSolicitud($id_proyecto, $id_solicitante, $titulo, $descripcion) {
        $sql = "INSERT INTO SolicitudesCambio 
                    (id_proyecto, id_solicitante, titulo, descripcion_detallada, fecha_solicitud, estado_sc)
                VALUES (?, ?, ?, ?, NOW(), 'Registrada')";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("SolicitudCambioModel::crearSolicitud prepare error: " . $this->db->error);
            return false;
        }
        $stmt->bind_param("iiss", $id_proyecto, $id_solicitante, $titulo, $descripcion);
        if ($stmt->execute()) {
            $newId = $stmt->insert_id;
            $stmt->close();
            return $newId;
        } else {
            error_log("SolicitudCambioModel::crearSolicitud execute error: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    public function guardarArchivo(int $id_sc, string $nombre, string $tipo, string $ruta): bool {
        $sql  = "INSERT INTO ArchivosAdjuntosSC (id_sc, nombre_archivo, tipo_archivo, ruta_archivo)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isss", $id_sc, $nombre, $tipo, $ruta);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function obtenerAdjuntos($id_solicitud) {
        $sql = "SELECT id_adjunto_sc, nombre_archivo, tipo_archivo, ruta_archivo, fecha_subida
                FROM ArchivosAdjuntosSC
                WHERE id_sc = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_solicitud);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }

    public function obtenerArchivosPorSolicitud(int $id_solicitud): array
    {
        $sql = "SELECT 
                    id_adjunto_sc, 
                    nombre_archivo, 
                    ruta_archivo 
                FROM ArchivosAdjuntosSC
                WHERE id_sc = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_solicitud);
        $stmt->execute();
        $res = $stmt->get_result();
        $archivos = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $archivos;
    }

    public function obtenerArchivoPorId(int $id_adjunto): ?array
    {
        $sql = "SELECT id_adjunto_sc, nombre_archivo, tipo_archivo, ruta_archivo
                FROM ArchivosAdjuntosSC
                WHERE id_adjunto_sc = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param("i", $id_adjunto);
        $stmt->execute();
        $file = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $file ?: null;
    }

    public function obtenerTodasLasSolicitudes() {
        $sql = "SELECT 
                sc.id_sc       AS id_solicitud,
                sc.id_proyecto,
                p.nombre_proyecto,
                sc.id_solicitante,
                sc.titulo,
                sc.descripcion_detallada AS descripcion,
                sc.fecha_solicitud      AS fecha_creacion,
                sc.estado_sc            AS estado,
                u.nombre_completo       AS nombre_completo
            FROM SolicitudesCambio sc
            JOIN Usuarios u   ON sc.id_solicitante = u.id_usuario
            JOIN Proyectos p  ON sc.id_proyecto   = p.id_proyecto
            ORDER BY sc.fecha_solicitud DESC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerSolicitudesPorEstado($estado) {
        $sql = "SELECT id_sc AS id_solicitud, titulo
                FROM SolicitudesCambio
                WHERE estado_sc = ?
                ORDER BY fecha_solicitud DESC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param("s", $estado);
        $stmt->execute();
        $arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $arr;
    }

    public function obtenerSolicitudPorId($id_solicitud) {
        $sql = "SELECT 
                sc.id_sc       AS id_solicitud,
                sc.id_proyecto,
                p.nombre_proyecto,
                sc.id_solicitante,
                sc.titulo,
                sc.descripcion_detallada AS descripcion,
                sc.fecha_solicitud      AS fecha_creacion,
                sc.estado_sc            AS estado,
                u.nombre_completo       AS nombre_completo
            FROM SolicitudesCambio sc
            JOIN Usuarios u   ON sc.id_solicitante = u.id_usuario
            JOIN Proyectos p  ON sc.id_proyecto   = p.id_proyecto
            WHERE sc.id_sc = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param("i", $id_solicitud);
        $stmt->execute();
        $sol = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $sol;
    }

    public function actualizarSolicitud($id_solicitud, $titulo, $descripcion) {
        $sql = "UPDATE SolicitudesCambio
                SET titulo = ?, descripcion_detallada = ?
                WHERE id_sc = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("ssi", $titulo, $descripcion, $id_solicitud);
        $ok = $stmt->execute();
        if (!$ok) error_log("SolicitudCambioModel::actualizarSolicitud error: " . $stmt->error);
        $stmt->close();
        return $ok;
    }

    public function eliminarSolicitud($id_solicitud) {
        $sql = "DELETE FROM SolicitudesCambio WHERE id_sc = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("i", $id_solicitud);
        $ok = $stmt->execute();
        if (!$ok) error_log("SolicitudCambioModel::eliminarSolicitud error: " . $stmt->error);
        $stmt->close();
        return $ok;
    }

    public function actualizarAnalisisImpacto(int $id_solicitud, string $analisis): bool
    {
        $sql = "UPDATE SolicitudesCambio
                SET analisis_impacto = ?, estado_sc = 'En Análisis'
                WHERE id_sc = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("si", $analisis, $id_solicitud);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function actualizarDecisionFinal(int $id_solicitud, string $estado, string $decision): bool
    {
        $sql = "UPDATE SolicitudesCambio
                SET estado_sc = ?, 
                    decision_final = ?,
                    fecha_decision_final = NOW()
                WHERE id_sc = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Error al preparar actualizarDecisionFinal: " . $this->db->error);
            return false;
        }
        $stmt->bind_param("ssi", $estado, $decision, $id_solicitud);
        $ok = $stmt->execute();
        if (!$ok) {
            error_log("Error al ejecutar actualizarDecisionFinal: " . $stmt->error);
        }
        $stmt->close();
        return $ok;
    }
}
