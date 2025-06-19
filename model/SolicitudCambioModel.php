<?php
require_once __DIR__ . '/../config/database.php';

class SolicitudCambioModel {
    private $db;
    private $impacto_est;
    public function setImpactoEstimado(string $impacto){ $this->impacto_est = $impacto; }
    public function getImpactoEstimado(): ?string { return $this->impacto_est; }

    public function __construct() {
        try {
            $this->db = (new Conexion())->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en SolicitudCambioModel: " . $e->getMessage());
            throw $e;
        }
    }
   
    private function calcularImpacto(string $prioridad, string $tipo_cambio): array {
        $pesoPrioridad = ['ALTA' => 5, 'MEDIA' => 3, 'BAJA' => 1];
        $pesoTipo      = ['CORRECCION' => 2, 'MEJORA' => 3, 'NUEVA_FUNCIONALIDAD' => 4];

        $suma        = ($pesoPrioridad[$prioridad] ?? 0) + ($pesoTipo[$tipo_cambio] ?? 0);
        $max         = max($pesoPrioridad) + max($pesoTipo);
        $nivel       = (int) ceil($suma / $max * 5);
        $porcentaje  = (int) round($suma / $max * 100);

        return ['nivel' => $nivel, 'porcentaje' => $porcentaje];
    }

    public function crearSolicitud($id_proyecto, $id_solicitante, $prioridad, $tipo_cambio, $justificacion, $titulo, $descripcion) {
        $imp = $this->calcularImpacto($prioridad, $tipo_cambio);
        $nivel      = $imp['nivel'];
        $porcentaje = $imp['porcentaje'];

        $sql = "INSERT INTO SolicitudesCambio 
                    (id_proyecto, id_solicitante, titulo, descripcion_detallada, justificacion, prioridad, tipo_cambio, impacto, impacto_est, fecha_solicitud, estado_sc)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Registrada')";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("SolicitudCambioModel::crearSolicitud prepare error: " . $this->db->error);
            return false;
        }

        $stmt->bind_param(
            "iisssssii",
            $id_proyecto,
            $id_solicitante,
            $titulo,
            $descripcion,
            $justificacion,
            $prioridad,
            $tipo_cambio,
            $nivel,
            $porcentaje
        );

        if ($stmt->execute()) {
            $newId = $stmt->insert_id;
            $stmt->close();

            $codigo = 'SC-' . str_pad($newId, 6, '0', STR_PAD_LEFT);
            $upd = $this->db->prepare("UPDATE SolicitudesCambio SET codigo_sc = ? WHERE id_sc = ?");
            if ($upd) {
                $upd->bind_param("si", $codigo, $newId);
                $upd->execute();
                $upd->close();
            }

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
                    sc.id_sc                   AS id_solicitud,
                    sc.codigo_sc               AS codigo_sc,
                    sc.id_proyecto,
                    p.nombre_proyecto,
                    sc.id_solicitante,
                    u.nombre_completo          AS nombre_completo,
                    sc.titulo,
                    sc.descripcion_detallada   AS descripcion,
                    sc.justificacion,
                    sc.prioridad,
                    sc.tipo_cambio,
                    sc.impacto,
                    sc.impacto_est,
                    sc.fecha_solicitud         AS fecha_creacion,
                    sc.estado_sc               AS estado,
                    sc.analisis_impacto,
                    sc.decision_final,
                    sc.fecha_decision_final,
                    sc.fecha_ultima_modificacion
                FROM SolicitudesCambio sc
                JOIN Usuarios   u ON sc.id_solicitante = u.id_usuario
                JOIN Proyectos  p ON sc.id_proyecto   = p.id_proyecto
                ORDER BY sc.fecha_solicitud ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerSolicitudesPorEstado($estado) {
        $sql = "SELECT 
                    sc.id_sc           AS id_solicitud,
                    sc.codigo_sc       AS codigo_sc,
                    sc.titulo
                FROM SolicitudesCambio sc
                WHERE sc.estado_sc = ?
                ORDER BY sc.fecha_solicitud ASC";
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
                    sc.justificacion,
                    sc.prioridad,
                    sc.tipo_cambio,
                    sc.impacto,
                    sc.impacto_est,
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

    public function actualizarSolicitud($id_solicitud, $prioridad, $tipo_cambio, $justificacion, $titulo, $descripcion) {
        $imp = $this->calcularImpacto($prioridad, $tipo_cambio);
        $nivel      = $imp['nivel'];
        $porcentaje = $imp['porcentaje'];

        $sql = "UPDATE SolicitudesCambio
                SET prioridad             = ?,
                    tipo_cambio           = ?,
                    justificacion         = ?,
                    titulo                = ?,
                    descripcion_detallada = ?,
                    impacto               = ?,
                    impacto_est           = ?
                WHERE id_sc = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("SolicitudCambioModel::actualizarSolicitud prepare error: " . $this->db->error);
            return false;
        }

        $stmt->bind_param(
            "sssssiii",
            $prioridad,
            $tipo_cambio,
            $justificacion,
            $titulo,
            $descripcion,
            $nivel,
            $porcentaje,
            $id_solicitud
        );

        $ok = $stmt->execute();
        if (!$ok) {
            error_log("SolicitudCambioModel::actualizarSolicitud execute error: " . $stmt->error);
        }
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

    public function obtenerCountPorEstado(): array {
        $sql = "
          SELECT estado_sc AS estado, COUNT(*) AS total
          FROM SolicitudesCambio
          GROUP BY estado_sc
        ";
        $r = $this->db->query($sql);
        return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerSolicitudesFiltradas(
        int $id_proyecto,
        string $desde,
        string $hasta,
        array $estadosSC
    ): array {
        $params = [ $id_proyecto ];
        $types  = "i";
        $conds  = ["sc.id_proyecto = ?"];

        if ($desde !== '') {
            $conds[] = "sc.fecha_solicitud >= ?";
            $params[] = $desde . ' 00:00:00';
            $types .= "s";
        }
        if ($hasta !== '') {
            $conds[] = "sc.fecha_solicitud <= ?";
            $params[] = $hasta . ' 23:59:59';
            $types .= "s";
        }
        if (count($estadosSC)) {
            $in  = implode(",", array_fill(0, count($estadosSC), "?"));
            $conds[] = "sc.estado_sc IN ($in)";
            foreach ($estadosSC as $e) { $params[] = $e; $types .= "s"; }
        }

        $sql = "
          SELECT sc.estado_sc AS estado, COUNT(*) AS total
          FROM SolicitudesCambio sc
          WHERE ". implode(" AND ", $conds) ."
          GROUP BY sc.estado_sc
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }
}