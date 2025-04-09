-- Crear la Base de Datos (si no existe)
CREATE DATABASE IF NOT EXISTS clinica_empleados_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clinica_empleados_db;

-- -----------------------------------------------------
-- Tabla: Cargos
-- Almacena los diferentes tipos de cargos/roles en la clínica.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Cargos (
  id_cargo INT AUTO_INCREMENT PRIMARY KEY,
  nombre_cargo VARCHAR(100) NOT NULL UNIQUE COMMENT 'Ej: Médico General, Enfermero Jefe, Recepcionista, Cardiólogo',
  descripcion TEXT NULL COMMENT 'Descripción opcional del cargo',
  -- Podrías añadir un campo 'porcentaje_bonificacion_base' si quieres usarlo en el SP
  -- porcentaje_bonificacion_base DECIMAL(5, 2) DEFAULT 0.00
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Tabla de roles o cargos de los empleados';

-- Insertar algunos cargos iniciales (ejemplos)
INSERT INTO Cargos (nombre_cargo) VALUES
('Médico General'),
('Médico Especialista'),
('Enfermero/a'),
('Auxiliar de Enfermería'),
('Administrativo'),
('Recepcionista');

-- -----------------------------------------------------
-- Tabla: Departamentos (Opcional pero recomendado)
-- Para agrupar empleados por área funcional.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Departamentos (
  id_departamento INT AUTO_INCREMENT PRIMARY KEY,
  nombre_departamento VARCHAR(100) NOT NULL UNIQUE COMMENT 'Ej: Cardiología, Pediatría, Administración, Enfermería',
  ubicacion VARCHAR(100) NULL COMMENT 'Ej: Planta 2, Edificio A',
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Departamentos o áreas funcionales de la clínica';

-- Insertar algunos departamentos iniciales (ejemplos)
INSERT INTO Departamentos (nombre_departamento, ubicacion) VALUES
('Administración', 'Planta Baja'),
('Consulta Externa', 'Planta 1'),
('Enfermería General', 'Planta 2'),
('Cardiología', 'Planta 3'),
('Pediatría', 'Planta 3');


-- -----------------------------------------------------
-- Tabla: Especialidades (Específica para Médicos)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Especialidades (
    id_especialidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre_especialidad VARCHAR(100) NOT NULL UNIQUE COMMENT 'Ej: Cardiología, Neurología, Pediatría',
    descripcion TEXT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Especialidades médicas';

-- Insertar algunas especialidades iniciales (ejemplos)
INSERT INTO Especialidades (nombre_especialidad) VALUES
('Cardiología'),
('Pediatría'),
('Neurología'),
('Cirugía General'),
('Medicina Interna');

-- -----------------------------------------------------
-- Tabla: Empleados
-- Tabla principal para almacenar la información de todos los empleados.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Empleados (
  id_empleado INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  dni VARCHAR(20) NOT NULL UNIQUE COMMENT 'Documento Nacional de Identidad o similar',
  fecha_nacimiento DATE NULL,
  genero ENUM('Masculino', 'Femenino', 'Otro') NULL,
  direccion TEXT NULL,
  telefono VARCHAR(20) NULL,
  email VARCHAR(100) UNIQUE NULL,
  fecha_ingreso DATE NOT NULL COMMENT 'Fecha de inicio en la clínica',
  salario DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Salario base actual',
  id_cargo INT NOT NULL COMMENT 'FK a la tabla Cargos',
  id_departamento INT NULL COMMENT 'FK a la tabla Departamentos (opcional)',
  -- Campo para la eliminación lógica
  estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo' COMMENT 'Estado del empleado (para eliminación lógica)',
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  -- Constraints (Llaves Foráneas)
  FOREIGN KEY (id_cargo) REFERENCES Cargos(id_cargo) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (id_departamento) REFERENCES Departamentos(id_departamento) ON DELETE SET NULL ON UPDATE CASCADE -- O RESTRICT si prefieres

) ENGINE=InnoDB COMMENT='Tabla central de empleados de la clínica';

-- Añadir índices para mejorar búsquedas comunes
CREATE INDEX idx_empleado_nombre ON Empleados(nombre, apellido);
CREATE INDEX idx_empleado_cargo ON Empleados(id_cargo);
CREATE INDEX idx_empleado_estado ON Empleados(estado);

-- -----------------------------------------------------
-- Tabla: MedicoEspecialidad (Tabla de Unión Muchos a Muchos)
-- Relaciona a los empleados que son médicos con sus especialidades.
-- Un médico puede tener varias especialidades.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS MedicoEspecialidad (
    id_medico_especialidad INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT NOT NULL COMMENT 'FK a Empleados (debe ser un médico)',
    id_especialidad INT NOT NULL COMMENT 'FK a Especialidades',
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Constraints
    FOREIGN KEY (id_empleado) REFERENCES Empleados(id_empleado) ON DELETE CASCADE ON UPDATE CASCADE, -- Si se elimina el empleado, se elimina la relación
    FOREIGN KEY (id_especialidad) REFERENCES Especialidades(id_especialidad) ON DELETE RESTRICT ON UPDATE CASCADE,

    -- Evitar duplicados (un médico no puede tener la misma especialidad dos veces)
    UNIQUE KEY uq_medico_especialidad (id_empleado, id_especialidad)

) ENGINE=InnoDB COMMENT='Relaciona médicos con sus especialidades';


-- -----------------------------------------------------
-- Tabla: Horarios (Simplificada)
-- Almacena los horarios asignados a los empleados.
-- Esta es una versión simple, podría ser más compleja (turnos rotativos, etc.)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Horarios (
    id_horario INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT NOT NULL COMMENT 'FK a Empleados',
    dia_semana ENUM('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    -- Podrías añadir fechas de validez si los horarios cambian
    -- fecha_inicio_validez DATE NULL,
    -- fecha_fin_validez DATE NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Constraints
    FOREIGN KEY (id_empleado) REFERENCES Empleados(id_empleado) ON DELETE CASCADE ON UPDATE CASCADE,

    -- Evitar horarios solapados para el mismo empleado el mismo día podría requerir lógica adicional o triggers complejos.
    -- Por ahora, permitimos múltiples entradas por día/empleado (Ej: turno mañana y turno tarde)
    UNIQUE KEY uq_empleado_dia_inicio (id_empleado, dia_semana, hora_inicio) -- Evita duplicados exactos

) ENGINE=InnoDB COMMENT='Horarios de trabajo de los empleados';


-- -----------------------------------------------------
-- Tabla: AuditoriaSalarios
-- Registra los cambios realizados en el salario de los empleados (para el Trigger).
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS AuditoriaSalarios (
  id_auditoria INT AUTO_INCREMENT PRIMARY KEY,
  id_empleado INT NOT NULL COMMENT 'FK al empleado cuyo salario cambió',
  salario_anterior DECIMAL(10, 2) NOT NULL,
  salario_nuevo DECIMAL(10, 2) NOT NULL,
  fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  usuario_modificador VARCHAR(100) NULL COMMENT 'Usuario de BD o aplicación que hizo el cambio (puede obtenerse con USER() o CURRENT_USER())',

  -- Constraints
  FOREIGN KEY (id_empleado) REFERENCES Empleados(id_empleado) ON DELETE CASCADE ON UPDATE CASCADE -- O NO ACTION si prefieres mantener el log aunque se borre el empleado

) ENGINE=InnoDB COMMENT='Log de cambios en los salarios de los empleados';

-- -----------------------------------------------------
-- Tabla: Bonificaciones (Opcional, si quieres registrar las calculadas)
-- Podrías usar esta tabla para almacenar las bonificaciones calculadas por el SP.
-- -----------------------------------------------------
-- CREATE TABLE IF NOT EXISTS BonificacionesCalculadas (
--   id_bonificacion INT AUTO_INCREMENT PRIMARY KEY,
--   id_empleado INT NOT NULL,
--   mes INT NOT NULL,
--   anio INT NOT NULL,
--   monto_bonificacion DECIMAL(10, 2) NOT NULL,
--   fecha_calculo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--   calculado_por_sp BOOLEAN DEFAULT TRUE, -- Para identificar las calculadas automáticamente
--
--   FOREIGN KEY (id_empleado) REFERENCES Empleados(id_empleado) ON DELETE CASCADE,
--   UNIQUE KEY uq_empleado_periodo (id_empleado, mes, anio) -- Solo una bonificación por empleado por periodo
-- ) ENGINE=InnoDB COMMENT='Registro de bonificaciones calculadas';

-- -----------------------------------------------------

COMMIT; -- Finaliza la transacción de creación de tablas (buena práctica)