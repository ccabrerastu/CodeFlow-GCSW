<?php
require_once __DIR__ . '/../config/database.php';

class OrdenCambioModel {
    private $db;

    public function __construct() {
        $this->db = (new Conexion())->getConexion();
    }

    public function crearOrden($id_solicitud, $id_responsable, $descripcion_oc) {
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
                    (id_sc_origen, id_proyecto, id_responsable_implementacion, descripcion_oc, fecha_generacion, estado_oc)
                VALUES (?, ?, ?, ?, NOW(), 'Asignada')";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("OrdenCambioModel::crearOrden prepare error: " . $this->db->error);
            return false;
        }
        $stmt->bind_param("iiis", $id_solicitud, $id_proyecto, $id_responsable, $descripcion_oc);
        if ($stmt->execute()) {
            $newId = $stmt->insert_id;
            $stmt->close();

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
                    oc.id_oc       AS id_orden,
                    oc.id_sc_origen AS id_solicitud,
                    oc.id_proyecto,
                    oc.id_responsable_implementacion AS id_responsable,
                    oc.descripcion_oc               AS descripcion,
                    oc.fecha_generacion             AS fecha_creacion,
                    oc.estado_oc                    AS estado,
                    sc.titulo                      AS titulo_solicitud,
                    u.nombre_completo               AS nombre_creador
                FROM OrdenesCambio oc
                JOIN SolicitudesCambio sc ON oc.id_sc_origen = sc.id_sc
                JOIN Usuarios u           ON oc.id_responsable_implementacion = u.id_usuario
                ORDER BY oc.fecha_generacion DESC";
        $r = $this->db->query($sql);
        return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerOrdenPorId($id_orden) {
        $sql = "SELECT
                    oc.id_oc       AS id_orden,
                    oc.id_sc_origen AS id_solicitud,
                    oc.id_proyecto,
                    oc.id_responsable_implementacion AS id_responsable,
                    oc.descripcion_oc               AS descripcion,
                    oc.fecha_generacion             AS fecha_creacion,
                    oc.estado_oc                    AS estado,
                    sc.titulo                      AS titulo_solicitud,
                    u.nombre_completo               AS nombre_creador
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
}
