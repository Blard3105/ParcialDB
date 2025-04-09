<?php
// src/Controllers/EmpleadoController.php
namespace App\Controllers;

// Incluir modelos necesarios
// En un sistema con autoloading (Composer), esto sería automático.
require_once __DIR__ . '/../Models/Database.php';
require_once __DIR__ . '/../Models/Empleado.php';

// Usar namespaces
use App\Models\Empleado;

class EmpleadoController {

    private $empleadoModel;

    public function __construct() {
        $this->empleadoModel = new Empleado();
        // Iniciar sesión si no está iniciada (para mensajes flash)
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Muestra la lista de empleados
    public function index() {
        $estadoFiltro = $_GET['estado'] ?? 'Activo'; // Por defecto, mostrar activos
        if (!in_array($estadoFiltro, ['Activo', 'Inactivo', 'Todos'])) {
            $estadoFiltro = 'Activo'; // Valor por defecto seguro
        }

        $empleados = $this->empleadoModel->getAll($estadoFiltro);

        // Cargar la vista y pasarle los datos
        require_once __DIR__ . '/../Views/empleados/index.view.php';
    }

    // Muestra el formulario para crear un nuevo empleado
    public function create() {
        // Obtener datos necesarios para los selects del formulario
        $cargos = $this->empleadoModel->getAllCargos();
        $departamentos = $this->empleadoModel->getAllDepartamentos();

        require_once __DIR__ . '/../Views/empleados/crear.view.php';
    }

    // Procesa el formulario de creación
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- Validación básica (¡MEJORAR!) ---
            if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['dni']) || empty($_POST['fecha_ingreso']) || empty($_POST['id_cargo'])) {
                 $_SESSION['mensaje'] = "Error: Faltan campos obligatorios.";
                 $_SESSION['mensaje_tipo'] = 'alert-danger';
                 // Idealmente, redirigir de nuevo al formulario con los datos previos
                 header('Location: ' . BASE_URL . '?action=create');
                 exit;
            }

            // Asignar datos del POST al modelo
            $this->empleadoModel->nombre = $_POST['nombre'];
            $this->empleadoModel->apellido = $_POST['apellido'];
            $this->empleadoModel->dni = $_POST['dni'];
            $this->empleadoModel->fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
            $this->empleadoModel->genero = $_POST['genero'] ?? null;
            $this->empleadoModel->direccion = $_POST['direccion'] ?? null;
            $this->empleadoModel->telefono = $_POST['telefono'] ?? null;
            $this->empleadoModel->email = $_POST['email'] ?? null;
            $this->empleadoModel->fecha_ingreso = $_POST['fecha_ingreso'];
            $this->empleadoModel->salario = $_POST['salario'] ?? 0.00;
            $this->empleadoModel->id_cargo = $_POST['id_cargo'];
            $this->empleadoModel->id_departamento = !empty($_POST['id_departamento']) ? $_POST['id_departamento'] : null;

            // Intentar guardar
            if ($this->empleadoModel->create()) {
                $_SESSION['mensaje'] = "Empleado registrado exitosamente.";
                $_SESSION['mensaje_tipo'] = 'alert-success';
            } else {
                 $_SESSION['mensaje'] = "Error al registrar el empleado.";
                 $_SESSION['mensaje_tipo'] = 'alert-danger';
                 // Podrías querer redirigir a create en caso de error también
            }
             header('Location: ' . BASE_URL . '?action=index'); // Redirigir siempre al listado
             exit;
        } else {
            // Si no es POST, redirigir a la página de creación
            header('Location: ' . BASE_URL . '?action=create');
            exit;
        }
    }

    // Muestra el formulario para editar un empleado
    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
             $_SESSION['mensaje'] = "ID de empleado no válido.";
             $_SESSION['mensaje_tipo'] = 'alert-danger';
             header('Location: ' . BASE_URL . '?action=index');
             exit;
        }

        $empleado = $this->empleadoModel->findById($id);

        if (!$empleado) {
             $_SESSION['mensaje'] = "Empleado no encontrado.";
             $_SESSION['mensaje_tipo'] = 'alert-danger';
             header('Location: ' . BASE_URL . '?action=index');
             exit;
        }

        // Obtener cargos y departamentos para los selects
        $cargos = $this->empleadoModel->getAllCargos();
        $departamentos = $this->empleadoModel->getAllDepartamentos();

        require_once __DIR__ . '/../Views/empleados/editar.view.php';
    }

    // Procesa el formulario de edición
    public function update() {
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_empleado'] ?? null;

            // --- Validación básica (¡MEJORAR!) ---
             if (!$id || !filter_var($id, FILTER_VALIDATE_INT) || empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['dni']) || empty($_POST['fecha_ingreso']) || empty($_POST['id_cargo'])) {
                 $_SESSION['mensaje'] = "Error: Faltan datos obligatorios o ID inválido.";
                 $_SESSION['mensaje_tipo'] = 'alert-danger';
                 // Redirigir de vuelta a editar si es posible, o al índice
                 header('Location: ' . ($id ? BASE_URL . '?action=edit&id='.$id : BASE_URL . '?action=index') );
                 exit;
            }

            // Asignar datos del POST al modelo
            // Es importante asignar el ID también para la cláusula WHERE
            $this->empleadoModel->id_empleado = $id;
            $this->empleadoModel->nombre = $_POST['nombre'];
            $this->empleadoModel->apellido = $_POST['apellido'];
            $this->empleadoModel->dni = $_POST['dni'];
            $this->empleadoModel->fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
            $this->empleadoModel->genero = $_POST['genero'] ?? null;
            $this->empleadoModel->direccion = $_POST['direccion'] ?? null;
            $this->empleadoModel->telefono = $_POST['telefono'] ?? null;
            $this->empleadoModel->email = $_POST['email'] ?? null;
            $this->empleadoModel->fecha_ingreso = $_POST['fecha_ingreso'];
            $this->empleadoModel->salario = $_POST['salario'] ?? 0.00;
            $this->empleadoModel->id_cargo = $_POST['id_cargo'];
            $this->empleadoModel->id_departamento = !empty($_POST['id_departamento']) ? $_POST['id_departamento'] : null;

             // Intentar actualizar
            if ($this->empleadoModel->update()) {
                 $_SESSION['mensaje'] = "Empleado actualizado exitosamente.";
                 $_SESSION['mensaje_tipo'] = 'alert-success';
            } else {
                 // Podría ser un error o que no hubo cambios
                 // Una comprobación más fina podría ser necesaria si quieres distinguir
                 $_SESSION['mensaje'] = "No se pudo actualizar el empleado (o no hubo cambios).";
                 $_SESSION['mensaje_tipo'] = 'alert-warning'; // Usar warning si no hubo cambios
            }
             header('Location: ' . BASE_URL . '?action=index'); // Redirigir al listado
             exit;

        } else {
             // Si no es POST, redirigir al índice
            header('Location: ' . BASE_URL . '?action=index');
            exit;
        }
    }

    // Cambia el estado (Activo/Inactivo)
    public function toggleStatus() {
        $id = $_GET['id'] ?? null;
        if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
             $_SESSION['mensaje'] = "ID de empleado no válido.";
             $_SESSION['mensaje_tipo'] = 'alert-danger';
             header('Location: ' . BASE_URL . '?action=index');
             exit;
        }

        // Obtener el empleado para saber su estado actual
        $empleado = $this->empleadoModel->findById($id);

        if (!$empleado) {
             $_SESSION['mensaje'] = "Empleado no encontrado.";
             $_SESSION['mensaje_tipo'] = 'alert-danger';
             header('Location: ' . BASE_URL . '?action=index');
             exit;
        }

        // Determinar el nuevo estado
        $nuevoEstado = ($empleado->estado === 'Activo') ? 'Inactivo' : 'Activo';

        if ($this->empleadoModel->toggleStatus($id, $nuevoEstado)) {
             $_SESSION['mensaje'] = "Estado del empleado cambiado a {$nuevoEstado}.";
             $_SESSION['mensaje_tipo'] = 'alert-success';
        } else {
             $_SESSION['mensaje'] = "Error al cambiar el estado del empleado.";
             $_SESSION['mensaje_tipo'] = 'alert-danger';
        }
         header('Location: ' . BASE_URL . '?action=index'); // Redirigir al listado
         exit;
    }

     // Ejemplo de cómo llamarías a la transacción (necesitaría un formulario/ruta aparte)
     public function registrarNuevoMedico() {
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger datos del formulario (similar a store)
            $datosEmpleado = [
                 'nombre' => $_POST['nombre'],
                 'apellido' => $_POST['apellido'],
                 // ... todos los demás campos de Empleados
                 'id_cargo' => $_POST['id_cargo'], // Asegúrate que sea un ID de cargo de médico
                 // ...
             ];
             $id_especialidad = $_POST['id_especialidad'] ?? null; // Asumiendo un select en el form
             $horarios = []; // Recoger los horarios del form (ej: campos dinámicos)
             // Ejemplo de cómo podrían venir los horarios:
             // $horarios = [
             //    ['dia_semana' => 'Lunes', 'hora_inicio' => '08:00', 'hora_fin' => '14:00'],
             //    ['dia_semana' => 'Martes', 'hora_inicio' => '08:00', 'hora_fin' => '14:00'],
             // ];

             $resultado = $this->empleadoModel->crearMedicoCompleto($datosEmpleado, $id_especialidad, $horarios);

             if ($resultado) {
                 $_SESSION['mensaje'] = "Nuevo médico registrado exitosamente con ID: " . $resultado;
                 $_SESSION['mensaje_tipo'] = 'alert-success';
             } else {
                  $_SESSION['mensaje'] = "Error en la transacción al registrar el médico.";
                  $_SESSION['mensaje_tipo'] = 'alert-danger';
             }
              header('Location: ' . BASE_URL . '?action=index'); // O a una página específica
              exit;
         } else {
             // Mostrar el formulario específico para registrar médicos
             // $cargosMedicos = $this->empleadoModel->getCargosPorTipo('Medico'); // Necesitarías un método así
             // $especialidades = $this->empleadoModel->getAllEspecialidades(); // Necesitarías un método así
             // require_once __DIR__ . '/../Views/empleados/crear_medico.view.php'; // Crear esta vista
         }
     }
}
?>