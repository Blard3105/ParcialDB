<?php
// src/Models/Empleado.php
namespace App\Models;

use PDO;

class Empleado {
    private $conn;
    private $table = 'Empleados'; // Nombre exacto de la tabla

    // Propiedades del Empleado (coinciden con columnas de la tabla)
    public $id_empleado;
    public $nombre;
    public $apellido;
    public $dni;
    public $fecha_nacimiento;
    public $genero;
    public $direccion;
    public $telefono;
    public $email;
    public $fecha_ingreso;
    public $salario;
    public $id_cargo;
    public $id_departamento;
    public $estado;

    // Propiedades adicionales para mostrar nombres (opcional)
    public $nombre_cargo;
    public $nombre_departamento;


    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Obtener todos los empleados (con filtro opcional por estado)
    public function getAll($estado = 'Activo') {
        $query = "SELECT
                    e.*, -- Selecciona todas las columnas de Empleados
                    c.nombre_cargo,
                    d.nombre_departamento
                  FROM {$this->table} e
                  LEFT JOIN Cargos c ON e.id_cargo = c.id_cargo
                  LEFT JOIN Departamentos d ON e.id_departamento = d.id_departamento";

        $conditions = [];
        $params = [];

        if ($estado === 'Activo' || $estado === 'Inactivo') {
            $conditions[] = "e.estado = :estado";
            $params[':estado'] = $estado;
        } // Si no es Activo ni Inactivo, no filtramos por estado (mostramos Todos)

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }

        $query .= " ORDER BY e.apellido ASC, e.nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ); // Devuelve objetos
    }

     // Obtener un solo empleado por ID
    public function findById($id) {
        $query = "SELECT
                    e.*,
                    c.nombre_cargo,
                    d.nombre_departamento
                  FROM {$this->table} e
                  LEFT JOIN Cargos c ON e.id_cargo = c.id_cargo
                  LEFT JOIN Departamentos d ON e.id_departamento = d.id_departamento
                  WHERE e.id_empleado = :id_empleado
                  LIMIT 1"; // Asegura que solo devuelva uno

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_empleado', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_OBJ); // Devuelve un objeto o false si no encuentra
         if ($row) {
            // Asignar valores a las propiedades del objeto actual (opcional)
             $this->id_empleado = $row->id_empleado;
             $this->nombre = $row->nombre;
             $this->apellido = $row->apellido;
             $this->dni = $row->dni;
             $this->fecha_nacimiento = $row->fecha_nacimiento;
             $this->genero = $row->genero;
             $this->direccion = $row->direccion;
             $this->telefono = $row->telefono;
             $this->email = $row->email;
             $this->fecha_ingreso = $row->fecha_ingreso;
             $this->salario = $row->salario;
             $this->id_cargo = $row->id_cargo;
             $this->id_departamento = $row->id_departamento;
             $this->estado = $row->estado;
             $this->nombre_cargo = $row->nombre_cargo;
             $this->nombre_departamento = $row->nombre_departamento;
             return $row; // Devolver el objeto encontrado
         }
         return false;
    }

    // Crear un nuevo empleado
    public function create() {
         // Verificar campos obligatorios básicos (mejor validación en Controller)
         if (empty($this->nombre) || empty($this->apellido) || empty($this->dni) || empty($this->fecha_ingreso) || empty($this->id_cargo)) {
            return false; // O lanzar una excepción
        }

        $query = "INSERT INTO {$this->table}
                    (nombre, apellido, dni, fecha_nacimiento, genero, direccion, telefono, email, fecha_ingreso, salario, id_cargo, id_departamento, estado)
                  VALUES
                    (:nombre, :apellido, :dni, :fecha_nacimiento, :genero, :direccion, :telefono, :email, :fecha_ingreso, :salario, :id_cargo, :id_departamento, :estado)";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos (sanitización básica - ¡mejorar!)
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));
        $this->dni = htmlspecialchars(strip_tags($this->dni));
        // ... (sanitizar otros campos según sea necesario)

         // Asignar valores por defecto si están vacíos/nulos donde la BD lo permita
         $this->fecha_nacimiento = !empty($this->fecha_nacimiento) ? $this->fecha_nacimiento : null;
         $this->genero = !empty($this->genero) ? $this->genero : null;
         $this->direccion = !empty($this->direccion) ? $this->direccion : null;
         $this->telefono = !empty($this->telefono) ? $this->telefono : null;
         $this->email = !empty($this->email) ? $this->email : null;
         $this->salario = !empty($this->salario) ? $this->salario : 0.00; // Salario por defecto
         $this->id_departamento = !empty($this->id_departamento) ? $this->id_departamento : null;
         $this->estado = 'Activo'; // Los nuevos empleados empiezan como Activos

        // Bind de parámetros
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':dni', $this->dni);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
        $stmt->bindParam(':genero', $this->genero);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':fecha_ingreso', $this->fecha_ingreso);
        $stmt->bindParam(':salario', $this->salario);
        $stmt->bindParam(':id_cargo', $this->id_cargo, PDO::PARAM_INT);
        $stmt->bindParam(':id_departamento', $this->id_departamento, PDO::PARAM_INT);
         $stmt->bindParam(':estado', $this->estado);


        if ($stmt->execute()) {
            $this->id_empleado = $this->conn->lastInsertId(); // Obtener el ID del nuevo empleado
            return true;
        }

        // Imprimir error si falla (solo para depuración)
        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Actualizar un empleado existente
    public function update() {
        // Verificar campos obligatorios
        if (empty($this->id_empleado) || empty($this->nombre) || empty($this->apellido) || empty($this->dni) || empty($this->fecha_ingreso) || empty($this->id_cargo)) {
            return false;
        }

        $query = "UPDATE {$this->table} SET
                    nombre = :nombre,
                    apellido = :apellido,
                    dni = :dni,
                    fecha_nacimiento = :fecha_nacimiento,
                    genero = :genero,
                    direccion = :direccion,
                    telefono = :telefono,
                    email = :email,
                    fecha_ingreso = :fecha_ingreso,
                    salario = :salario,
                    id_cargo = :id_cargo,
                    id_departamento = :id_departamento
                    -- No actualizamos 'estado' aquí, usamos toggleStatus para eso
                  WHERE id_empleado = :id_empleado";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));
        // ... (sanitizar otros)

        // Asignar valores por defecto si están vacíos/nulos donde la BD lo permita
         $this->fecha_nacimiento = !empty($this->fecha_nacimiento) ? $this->fecha_nacimiento : null;
         $this->genero = !empty($this->genero) ? $this->genero : null;
         $this->direccion = !empty($this->direccion) ? $this->direccion : null;
         $this->telefono = !empty($this->telefono) ? $this->telefono : null;
         $this->email = !empty($this->email) ? $this->email : null;
         $this->salario = !empty($this->salario) ? $this->salario : 0.00;
         $this->id_departamento = !empty($this->id_departamento) ? $this->id_departamento : null;

        // Bind de parámetros
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':dni', $this->dni);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
        $stmt->bindParam(':genero', $this->genero);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':fecha_ingreso', $this->fecha_ingreso);
        $stmt->bindParam(':salario', $this->salario);
        $stmt->bindParam(':id_cargo', $this->id_cargo, PDO::PARAM_INT);
        $stmt->bindParam(':id_departamento', $this->id_departamento, PDO::PARAM_INT);
        $stmt->bindParam(':id_empleado', $this->id_empleado, PDO::PARAM_INT);

        if ($stmt->execute()) {
             // Verificar si alguna fila fue afectada
             return $stmt->rowCount() > 0; // Devuelve true si se actualizó algo, false si no hubo cambios o no se encontró el ID
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Cambiar estado (Eliminación/Activación Lógica)
    public function toggleStatus($id, $nuevoEstado) {
        if ($nuevoEstado !== 'Activo' && $nuevoEstado !== 'Inactivo') {
            return false; // Estado no válido
        }

        $query = "UPDATE {$this->table} SET estado = :estado WHERE id_empleado = :id_empleado";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id_empleado', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // Devuelve true si se actualizó la fila
        }
        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // --- Métodos auxiliares para obtener datos relacionados (Cargos, Departamentos) ---
    //     Estos podrían estar en sus propios Models (Cargo.php, Departamento.php)
    //     pero los ponemos aquí por simplicidad para el ejemplo.

    public function getAllCargos() {
        $query = "SELECT id_cargo, nombre_cargo FROM Cargos ORDER BY nombre_cargo";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAllDepartamentos() {
        $query = "SELECT id_departamento, nombre_departamento FROM Departamentos ORDER BY nombre_departamento";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

     // --- Métodos para la transacción de Médico (Ejemplo) ---
     //     Estos idealmente irían en un `MedicoModel` o `EmpleadoService`

     public function crearMedicoCompleto($datosEmpleado, $id_especialidad, $horarios) {
        $this->conn->beginTransaction();
        try {
            // 1. Crear el empleado base
            $this->nombre = $datosEmpleado['nombre'];
            $this->apellido = $datosEmpleado['apellido'];
            $this->dni = $datosEmpleado['dni'];
            $this->fecha_nacimiento = $datosEmpleado['fecha_nacimiento'];
            $this->genero = $datosEmpleado['genero'];
            $this->direccion = $datosEmpleado['direccion'];
            $this->telefono = $datosEmpleado['telefono'];
            $this->email = $datosEmpleado['email'];
            $this->fecha_ingreso = $datosEmpleado['fecha_ingreso'];
            $this->salario = $datosEmpleado['salario'];
            $this->id_cargo = $datosEmpleado['id_cargo']; // Asegurarse que sea un cargo de Médico
            $this->id_departamento = $datosEmpleado['id_departamento'];

            if (!$this->create()) {
                 throw new \Exception("Error al crear el empleado base.");
            }
            $idNuevoEmpleado = $this->id_empleado; // ID obtenido tras la inserción

            // 2. Asignar Especialidad (Asumiendo una por ahora para simplificar)
            if (!empty($id_especialidad)) {
                $queryEsp = "INSERT INTO MedicoEspecialidad (id_empleado, id_especialidad) VALUES (:id_empleado, :id_especialidad)";
                $stmtEsp = $this->conn->prepare($queryEsp);
                $stmtEsp->bindParam(':id_empleado', $idNuevoEmpleado, PDO::PARAM_INT);
                $stmtEsp->bindParam(':id_especialidad', $id_especialidad, PDO::PARAM_INT);
                if (!$stmtEsp->execute()) {
                    throw new \Exception("Error al asignar la especialidad.");
                }
            }

            // 3. Asignar Horarios (Asumiendo $horarios es un array de arrays ['dia_semana', 'hora_inicio', 'hora_fin'])
            if (!empty($horarios) && is_array($horarios)) {
                $queryHor = "INSERT INTO Horarios (id_empleado, dia_semana, hora_inicio, hora_fin) VALUES (:id_empleado, :dia_semana, :hora_inicio, :hora_fin)";
                $stmtHor = $this->conn->prepare($queryHor);
                foreach ($horarios as $horario) {
                    // Validar horario aquí si es necesario
                    $stmtHor->bindParam(':id_empleado', $idNuevoEmpleado, PDO::PARAM_INT);
                    $stmtHor->bindParam(':dia_semana', $horario['dia_semana']);
                    $stmtHor->bindParam(':hora_inicio', $horario['hora_inicio']);
                    $stmtHor->bindParam(':hora_fin', $horario['hora_fin']);
                    if (!$stmtHor->execute()) {
                        throw new \Exception("Error al asignar horario para {$horario['dia_semana']}.");
                    }
                }
            }

            // Si todo fue bien, confirmar la transacción
            $this->conn->commit();
            return $idNuevoEmpleado; // Devolver el ID del nuevo médico

        } catch (\Exception $e) {
            // Si algo falla, deshacer todos los cambios
            $this->conn->rollBack();
            // Loguear el error $e->getMessage()
            return false; // Indicar que la operación falló
        }
    }

}
?>