<?php
// public/index.php

// Incluir configuración y clases base
require_once __DIR__ . '/../src/Config/config.php';
require_once __DIR__ . '/../src/Controllers/EmpleadoController.php';

// Usar el namespace del controlador
use App\Controllers\EmpleadoController;

// Determinar la acción solicitada (por defecto: 'index')
$action = $_GET['action'] ?? 'index';

// Crear una instancia del controlador
$controller = new EmpleadoController();

// Enrutamiento simple basado en el parámetro 'action'
switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'create':
        $controller->create();
        break;
    case 'store':
        $controller->store();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'update':
        $controller->update();
        break;
    case 'toggleStatus': // Para activar/desactivar (eliminación lógica)
        $controller->toggleStatus();
        break;
    // --- Ejemplo para la transacción ---
    // case 'createMedico': // Ruta para mostrar el form de nuevo médico
    //     $controller->registrarNuevoMedico(); // El método se encarga de mostrar form si no es POST
    //     break;
    // case 'storeMedico': // Ruta para procesar el form de nuevo médico
    //     $controller->registrarNuevoMedico(); // El método se encarga de procesar si es POST
    //     break;
    default:
        // Acción no encontrada, mostrar página 404 o redirigir al índice
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1><p>La página solicitada no existe.</p>";
        // O redirigir:
        // header('Location: ' . BASE_URL . '?action=index');
        exit;
}

?>