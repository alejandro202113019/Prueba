-- Eliminar la base de datos si existe y crearla nuevamente
DROP DATABASE IF EXISTS GestionHallazgos;
CREATE DATABASE GestionHallazgos;
USE GestionHallazgos;

-- Crear la tabla de procesos
CREATE TABLE Proceso (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Identificador único para cada proceso
    nombre VARCHAR(100) NOT NULL,       -- Nombre del proceso (ej. Control de Calidad)
    descripcion TEXT,                   -- Descripción detallada del proceso
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Fecha en la que se crea el registro
);

-- Crear la tabla de estados
CREATE TABLE Estado (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Identificador único para cada estado
    nombre VARCHAR(50) NOT NULL         -- Nombre del estado (ej. Abierto, Cerrado)
);

-- Crear la tabla de usuarios
CREATE TABLE Usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Identificador único para cada usuario
    nombre VARCHAR(100) NOT NULL,       -- Nombre completo del usuario
    email VARCHAR(100) NOT NULL UNIQUE, -- Correo electrónico único para cada usuario
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Fecha en la que se registra el usuario
);

-- Crear la tabla de hallazgos
CREATE TABLE Hallazgo (
    id INT AUTO_INCREMENT PRIMARY KEY,      -- Identificador único para cada hallazgo
    titulo VARCHAR(150) NOT NULL,           -- Título breve del hallazgo
    descripcion TEXT NOT NULL,              -- Descripción detallada del hallazgo
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha en la que se crea el hallazgo
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Fecha de última actualización
    id_estado INT,                          -- Estado actual del hallazgo (referencia a Estado)
    id_usuario INT,                         -- Usuario responsable del hallazgo (referencia a Usuario)
    FOREIGN KEY (id_estado) REFERENCES Estado(id),  -- Llave foránea a la tabla Estado
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id) -- Llave foránea a la tabla Usuario
);

-- Crear la tabla de incidentes
CREATE TABLE Incidente (
    id INT AUTO_INCREMENT PRIMARY KEY,      -- Identificador único para cada incidente
    descripcion TEXT NOT NULL,              -- Descripción detallada del incidente
    fecha_ocurrencia DATE NOT NULL,         -- Fecha en la que ocurrió el incidente
    id_estado INT,                          -- Estado actual del incidente (referencia a Estado)
    id_usuario INT,                         -- Usuario que reportó o es responsable del incidente (referencia a Usuario)
    FOREIGN KEY (id_estado) REFERENCES Estado(id),  -- Llave foránea a la tabla Estado
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id) -- Llave foránea a la tabla Usuario
);

-- Crear la tabla de planes de acción
CREATE TABLE PlanAccion (
    id INT AUTO_INCREMENT PRIMARY KEY,      -- Identificador único para cada plan de acción
    descripcion TEXT NOT NULL,              -- Descripción detallada del plan de acción
    id_usuario INT,                         -- Usuario responsable del plan de acción (referencia a Usuario)
    fecha_inicio DATE,                      -- Fecha de inicio del plan de acción
    fecha_fin DATE,                         -- Fecha de finalización del plan de acción
    id_estado INT,                          -- Estado actual del plan de acción (referencia a Estado)
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id), -- Llave foránea a la tabla Usuario
    FOREIGN KEY (id_estado) REFERENCES Estado(id)    -- Llave foránea a la tabla Estado
);

-- Crear la tabla de unión Registro_PlanAccion para la relación indirecta entre registros y PlanAccion
CREATE TABLE Registro_PlanAccion (
    id INT AUTO_INCREMENT PRIMARY KEY,        -- Identificador único para cada registro en la tabla
    id_registro INT NOT NULL,                 -- Identificador del registro
    origen_registro ENUM('INCIDENTE', 'HALLAZGO') NOT NULL,  -- Origen del registro
    id_plan_accion INT,                       -- Identificador del plan de acción asociado
    FOREIGN KEY (id_plan_accion) REFERENCES PlanAccion(id)   -- Llave foránea a la tabla PlanAccion
);

-- Crear la tabla intermedia Hallazgo_Proceso para la relación de muchos a muchos entre hallazgos y procesos
CREATE TABLE Hallazgo_Proceso (
    id_hallazgo INT,                          -- Identificador del hallazgo (referencia a Hallazgo)
    id_proceso INT,                           -- Identificador del proceso (referencia a Proceso)
    PRIMARY KEY (id_hallazgo, id_proceso),    -- Llave primaria compuesta para garantizar la relación única
    FOREIGN KEY (id_hallazgo) REFERENCES Hallazgo(id), -- Llave foránea a la tabla Hallazgo
    FOREIGN KEY (id_proceso) REFERENCES Proceso(id)   -- Llave foránea a la tabla Proceso
);

-- Insertar datos iniciales en la tabla Estado
INSERT INTO Estado (nombre) VALUES
    ('Abierto'),              -- Estado indicando que el registro está abierto
    ('En Proceso'),           -- Estado indicando que el registro está siendo trabajado
    ('Resuelto'),             -- Estado indicando que el registro se ha resuelto
    ('Cerrado');              -- Estado indicando que el registro ha sido cerrado

-- Insertar datos de ejemplo en la tabla Proceso
INSERT INTO Proceso (nombre, descripcion) VALUES
    ('Control de Calidad', 'Proceso de control de calidad en la producción de alimentos.'),
    ('Almacenamiento', 'Proceso de almacenamiento de productos en condiciones adecuadas.'),
    ('Distribución', 'Proceso de distribución de productos a diferentes destinos.'),
    ('Higiene y Seguridad', 'Control de higiene en las instalaciones y seguridad de empleados.'),
    ('Producción', 'Supervisión de la producción en la planta.'),
    ('Empaque', 'Proceso de empaque de productos alimenticios.'),
    ('Auditoría Interna', 'Auditoría interna para cumplimiento de normativas.'),
    ('Evaluación de Proveedores', 'Proceso de evaluación de proveedores de insumos.'),
    ('Regulación y Cumplimiento', 'Control de cumplimiento con regulaciones alimentarias.'),
    ('Capacitación', 'Capacitación de empleados en normas de seguridad alimentaria.');

-- Insertar datos de ejemplo en la tabla Usuario
INSERT INTO Usuario (nombre, email) VALUES
    ('Ana López', 'ana.lopez@empresa.com'),
    ('Carlos Martínez', 'carlos.martinez@empresa.com'),
    ('Laura Jiménez', 'laura.jimenez@empresa.com'),
    ('Fernando García', 'fernando.garcia@empresa.com'),
    ('Elena Rojas', 'elena.rojas@empresa.com'),
    ('David Torres', 'david.torres@empresa.com'),
    ('Luis Pérez', 'luis.perez@empresa.com'),
    ('Sofía Morales', 'sofia.morales@empresa.com'),
    ('Jorge Álvarez', 'jorge.alvarez@empresa.com'),
    ('Marta Sánchez', 'marta.sanchez@empresa.com');

-- Insertar datos de ejemplo en la tabla Hallazgo
INSERT INTO Hallazgo (titulo, descripcion, id_estado, id_usuario) VALUES
    ('Control de Temperatura', 'Problema en el control de temperatura en almacenamiento.', 1, 1),
    ('Higiene en Planta', 'Falta de desinfección en área de producción.', 2, 2),
    ('Etiquetado Incorrecto', 'Etiquetas en idioma incorrecto en empaque.', 1, 3),
    ('Retraso en Distribución', 'Retraso en la entrega de productos a sucursales.', 3, 4),
    ('Error en Inspección de Calidad', 'Inconsistencias en el control de calidad.', 2, 5),
    ('Almacenamiento Inadecuado', 'Producto almacenado en lugar sin ventilación.', 1, 6),
    ('Falta de Capacitación', 'Faltan capacitaciones sobre nuevas normativas.', 2, 7),
    ('Incumplimiento de Normativa', 'No se cumple normativa de empaque.', 3, 8),
    ('Problemas de Trazabilidad', 'Dificultad para rastrear origen de materia prima.', 1, 9),
    ('Evaluación de Proveedores Deficiente', 'Proveedor no cumple con estándares.', 2, 10);
    
-- Insertar datos de ejemplo en la tabla Hallazgo_Proceso para asociar hallazgos a múltiples procesos
INSERT INTO Hallazgo_Proceso (id_hallazgo, id_proceso) VALUES
    (1, 2), (1, 3), 
    (2, 4), (2, 5), 
    (3, 6), (3, 10), 
    (4, 3), (4, 9), 
    (5, 1), (5, 4), 
    (6, 2), (6, 8), 
    (7, 10), (7, 5), 
    (8, 6), (8, 9), 
    (9, 7), (9, 8), 
    (10, 5), (10, 7);

-- Insertar datos de ejemplo en la tabla Incidente
INSERT INTO Incidente (descripcion, fecha_ocurrencia, id_estado, id_usuario) VALUES
    ('Derrame de líquidos en zona de empaque', '2024-10-01', 1, 3),
    ('Fallo en equipo de refrigeración', '2024-09-20', 2, 5),
    ('Ingesta de producto contaminado', '2024-09-15', 1, 1),
    ('Personal sin equipo de protección', '2024-08-25', 3, 4),
    ('Desabastecimiento de insumos', '2024-08-30', 2, 6),
    ('Error en etiquetado de lote', '2024-07-18', 1, 7),
    ('Contaminación en línea de producción', '2024-06-22', 1, 2),
    ('Rotura de embalaje en almacenamiento', '2024-05-05', 4, 8),
    ('Incumplimiento de medidas de seguridad', '2024-04-17', 3, 10),
    ('Atraso en entrega de proveedor crítico', '2024-03-15', 2, 9);

-- Insertar datos de ejemplo en la tabla PlanAccion, asignando id_usuario como responsable
INSERT INTO PlanAccion (descripcion, id_usuario, fecha_inicio, fecha_fin, id_estado) VALUES
    ('Reparar equipo de refrigeración', 2, '2024-10-05', '2024-10-20', 2),
    ('Implementar sistema de etiquetas bilingües', 1, '2024-09-25', '2024-10-10', 1),
    ('Capacitar personal en manipulación de alimentos', 3, '2024-10-01', '2024-10-15', 2),
    ('Revisión del sistema de ventilación en almacenes', 4, '2024-09-15', '2024-09-30', 3),
    ('Cambio de proveedor para insumos', 5, '2024-08-20', '2024-09-10', 1),
    ('Actualizar procedimientos de limpieza', 6, '2024-07-01', '2024-07-15', 3),
    ('Revisión de trazabilidad del lote', 7, '2024-06-10', '2024-06-25', 4),
    ('Instalar señalización en zona de empaque', 8, '2024-05-10', '2024-05-25', 2),
    ('Evaluación de proveedores alternativos', 9, '2024-04-20', '2024-05-15', 1),
    ('Actualizar protocolos de emergencia', 10, '2024-03-01', '2024-03-15', 4);

-- Asociar planes de acción a incidentes en la tabla Registro_PlanAccion
INSERT INTO Registro_PlanAccion (id_registro, origen_registro, id_plan_accion) VALUES
    (1, 'INCIDENTE', 1),
    (2, 'INCIDENTE', 2),
    (3, 'INCIDENTE', 3),
    (4, 'INCIDENTE', 4),
    (5, 'INCIDENTE', 5),
    (6, 'INCIDENTE', 6),
    (7, 'INCIDENTE', 7),
    (8, 'INCIDENTE', 8),
    (9, 'INCIDENTE', 9),
    (10, 'INCIDENTE', 10);