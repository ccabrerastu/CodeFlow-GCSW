<?php
require_once __DIR__ . '/../config/database.php';

class OrdenCambioModel {
    private $db;

    public function __construct() {
        $this->db = (new Conexion())->getConexion();
    }

    public function crearOrden($id_solicitud, $id_responsable, $descripcion_oc, $f_inicio, $f_fin) {
        $prep = $this->db->prepare("SELECT id_proyecto FROM SolicitudesCambio WHERE id_sc = ?");
        if (!$prep) {
            error_log("OrdenCambioModel::crearOrden obtener proyecto prepare error: " . $this->db->error);
            return false;
        }
        $prep->bind_param("i", $id_solicitud);
        $prep->execute();
        $fila = $prep->get_result()->fetch_assoc();
        $prep->close();
        if (empty($fila['id_proyecto'])) {
            error_log("OrdenCambioModel::crearOrden no encontró proyecto para solicitud $id_solicitud");
            return false;
        }
        $id_proyecto = $fila['id_proyecto'];

        $sql = "INSERT INTO OrdenesCambio
                    (id_sc_origen,
                    id_proyecto,
                    id_responsable_implementacion,
                    descripcion_oc,
                    fecha_generacion,
                    estado_oc,
                    fecha_inicio_ejecucion_planificada,
                    fecha_fin_ejecucion_planificada)
                VALUES (?, ?, ?, ?, NOW(), 'Asignada', ?, ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("OrdenCambioModel::crearOrden prepare error: " . $this->db->error);
            return false;
        }

        $stmt->bind_param(
            "iiisss",
            $id_solicitud,
            $id_proyecto,
            $id_responsable,
            $descripcion_oc,
            $f_inicio,
            $f_fin
        );

        if ($stmt->execute()) {
            $newId = $stmt->insert_id;
            $stmt->close();

            $codigo = 'OC-' . str_pad($newId, 4, '0', STR_PAD_LEFT);
            $updCode = $this->db->prepare("UPDATE OrdenesCambio SET codigo_oc = ? WHERE id_oc = ?");
            if ($updCode) {
                $updCode->bind_param("si", $codigo, $newId);
                $updCode->execute();
                $updCode->close();
            }

            $upd = $this->db->prepare("UPDATE SolicitudesCambio SET estado_sc = 'En Orden' WHERE id_sc = ?");
            if ($upd) {
                $upd->bind_param("i", $id_solicitud);
                $upd->execute();
                $upd->close();
            }

            return $newId;
        } else {
            error_log("OrdenCambioModel::crearOrden execute error: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    public function obtenerTodasLasOrdenes() {
        $sql = "SELECT
                    oc.id_oc                                     AS id_orden,
                    oc.codigo_oc                                 AS codigo,
                    oc.id_sc_origen                              AS id_solicitud,
                    oc.id_proyecto,
                    oc.id_responsable_implementacion             AS id_responsable,
                    oc.descripcion_oc                            AS descripcion,
                    oc.fecha_generacion                          AS fecha_creacion,
                    oc.estado_oc                                 AS estado,
                    oc.fecha_inicio_ejecucion_planificada        AS fecha_inicio_planificada,
                    oc.fecha_fin_ejecucion_planificada           AS fecha_fin_planificada,
                    oc.fecha_inicio_ejecucion_real               AS fecha_inicio_real,
                    oc.fecha_fin_ejecucion_real                  AS fecha_fin_real,
                    oc.fecha_ultima_modificacion                 AS fecha_modificacion,
                    sc.titulo                                    AS titulo_solicitud,
                    u.nombre_completo                            AS nombre_creador
                FROM OrdenesCambio oc
                JOIN SolicitudesCambio sc ON oc.id_sc_origen = sc.id_sc
                JOIN Usuarios u           ON oc.id_responsable_implementacion = u.id_usuario
                ORDER BY oc.fecha_generacion ASC";
        $r = $this->db->query($sql);
        return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerOrdenPorId($id_orden) {
        $sql = "SELECT
                    oc.id_oc                                 AS id_orden,
                    oc.id_sc_origen                          AS id_solicitud,
                    oc.id_proyecto,
                    oc.id_responsable_implementacion         AS id_responsable,
                    oc.descripcion_oc                        AS descripcion,
                    oc.fecha_generacion                      AS fecha_creacion,
                    oc.estado_oc                             AS estado,
                    oc.fecha_inicio_ejecucion_planificada    AS fecha_inicio_planificada,
                    oc.fecha_fin_ejecucion_planificada       AS fecha_fin_planificada,
                    sc.titulo                                AS titulo_solicitud,
                    u.nombre_completo                        AS nombre_creador
                FROM OrdenesCambio oc
                JOIN SolicitudesCambio sc ON oc.id_sc_origen = sc.id_sc
                JOIN Usuarios u           ON oc.id_responsable_implementacion = u.id_usuario
                WHERE oc.id_oc = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param("i", $id_orden);
        $stmt->execute();
        $orden = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $orden;
    }

    public function eliminarOrden($id_orden) {
        $stmt = $this->db->prepare("DELETE FROM OrdenesCambio WHERE id_oc = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id_orden);
        $ok = $stmt->execute();
        if (!$ok) error_log("OrdenCambioModel::eliminarOrden error: " . $stmt->error);
        $stmt->close();
        return $ok;
    }

    public function actualizarEstadoOC(int $id_orden, string $nuevoEstado): bool {
        $sql = "UPDATE OrdenesCambio
                SET estado_oc = ?, fecha_ultima_modificacion = NOW()
                WHERE id_oc = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("OrdenCambioModel::actualizarEstadoOC prepare error: " . $this->db->error);
            return false;
        }
        $stmt->bind_param("si", $nuevoEstado, $id_orden);
        $ok = $stmt->execute();
        if (!$ok) error_log("OrdenCambioModel::actualizarEstadoOC execute error: " . $stmt->error);
        $stmt->close();
        return $ok;
    }

    public function agregarComentarioOC(int $id_orden, int $id_usuario, string $comentario): bool {
        $sql = "INSERT INTO ComentariosOC (id_oc, id_usuario, comentario)
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("OrdenCambioModel::agregarComentarioOC prepare error: " . $this->db->error);
            return false;
        }
        $stmt->bind_param("iis", $id_orden, $id_usuario, $comentario);
        $ok = $stmt->execute();
        if (!$ok) error_log("OrdenCambioModel::agregarComentarioOC execute error: " . $stmt->error);
        $stmt->close();
        return $ok;
    }

    public function obtenerComentariosOC(int $id_orden): array {
        $sql = "SELECT c.id_comentario_oc, u.nombre_completo, c.comentario, c.fecha_comentario
                FROM ComentariosOC c
                JOIN Usuarios u ON c.id_usuario = u.id_usuario
                WHERE c.id_oc = ?
                ORDER BY c.fecha_comentario ASC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param("i", $id_orden);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }

    public function obtenerCountPorEstado(): array {
        $sql = "
          SELECT estado_oc AS estado, COUNT(*) AS total
          FROM OrdenesCambio
          GROUP BY estado_oc
        ";
        $r = $this->db->query($sql);
        return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerOrdenesFiltradas(
        int $id_proyecto,
        string $desde,
        string $hasta,
        array $estadosOC
    ): array {
        $params = [ $id_proyecto ];
        $types  = "i";
        $conds  = ["oc.id_proyecto = ?"];

        if ($desde !== '') {
            $conds[] = "oc.fecha_generacion >= ?";
            $params[] = $desde . ' 00:00:00';
            $types .= "s";
        }
        if ($hasta !== '') {
            $conds[] = "oc.fecha_generacion <= ?";
            $params[] = $hasta . ' 23:59:59';
            $types .= "s";
        }
        if (count($estadosOC)) {
            $in = implode(",", array_fill(0, count($estadosOC), "?"));
            $conds[] = "oc.estado_oc IN ($in)";
            foreach ($estadosOC as $e) { $params[] = $e; $types .= "s"; }
        }

        $sql = "
          SELECT oc.estado_oc AS estado, COUNT(*) AS total
          FROM OrdenesCambio oc
          WHERE ". implode(" AND ", $conds) ."
          GROUP BY oc.estado_oc
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }
}
