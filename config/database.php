<?php
// Cargar las variables de entorno desde el archivo .env
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class Conexion {
    private $conexion;

    public function __construct() {
        // Obtener los valores de las variables de entorno
        $host = $_ENV['DB_HOST'];
        $usuario = $_ENV['DB_USER'];
        $contrasena = $_ENV['DB_PASS'];
        $base_de_datos = $_ENV['DB_NAME'];

        // Crear la conexión
        $this->conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

        // Verificar la conexión
        if ($this->conexion->connect_error) {
            die("Error en la conexión: " . $this->conexion->connect_error);
        }
    }

    public function getConexion() {
        return $this->conexion;
    }
}
?>
