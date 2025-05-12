<?

require_once  'config/config.php'; // Carga la configuración de las constantes


require_once "core/routes.php";
require_once "controllers/LoginControlador.php";

// Intenta ejecutar el código
try {
    
    // Controlador y acción
    if (isset($_GET['c'])) {
        $controlador = cargarControlador($_GET['c']);

        if (isset($_GET['a'])) {
            if (isset($_GET['id'])) {
                cargarAccion($controlador, $_GET['a'], $_GET['id']);
            } else {
                cargarAccion($controlador, $_GET['a']);
            }
        } else {
            cargarAccion($controlador, ACCION_PRINCIPAL);  
        }
    } else {
        $controlador = cargarControlador(CONTROLADOR_PRINCIPAL);  // Usa CONTROLADOR_PRINCIPAL, que es "LoginControlador"
        $accionTmp = ACCION_PRINCIPAL;  // "index"
        $controlador->$accionTmp();  
        
    }

} catch (Exception $e) {
   
    echo "Error: " . $e->getMessage();
    echo "<br>";
    echo "Archivo: " . $e->getFile();
    echo "<br>";
    echo "Línea: " . $e->getLine();
}
?>