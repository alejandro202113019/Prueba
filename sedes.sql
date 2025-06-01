-- migration_sedes.sql
-- Ejecutar este script en tu base de datos

USE GestionHallazgos;

-- Crear la tabla de sedes
CREATE TABLE Sedes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(200),
    ciudad VARCHAR(100),
    telefono VARCHAR(20),
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Agregar columna sede_id a la tabla Hallazgo
ALTER TABLE Hallazgo ADD COLUMN sede_id INT NULL;

-- Agregar foreign key constraint
ALTER TABLE Hallazgo ADD CONSTRAINT fk_hallazgo_sede 
    FOREIGN KEY (sede_id) REFERENCES Sedes(id);

-- Insertar datos de ejemplo en la tabla Sedes
INSERT INTO Sedes (nombre, direccion, ciudad, telefono, activa) VALUES
    ('Sede Principal', 'Calle 123 #45-67', 'Bogotá', '601-234-5678', TRUE),
    ('Sede Norte', 'Carrera 15 #78-90', 'Bogotá', '601-345-6789', TRUE),
    ('Sede Medellín', 'Avenida 80 #32-15', 'Medellín', '604-456-7890', TRUE),
    ('Sede Cali', 'Calle 5 #67-89', 'Cali', '602-567-8901', TRUE),
    ('Sede Barranquilla', 'Carrera 43 #23-45', 'Barranquilla', '605-678-9012', TRUE),
    ('Sede Temporal', 'Zona Industrial', 'Bogotá', '601-789-0123', FALSE);

-- Actualizar algunos hallazgos existentes con sedes
UPDATE Hallazgo SET sede_id = 1 WHERE id IN (1, 2);
UPDATE Hallazgo SET sede_id = 2 WHERE id IN (3, 4);
UPDATE Hallazgo SET sede_id = 3 WHERE id IN (5, 6);
UPDATE Hallazgo SET sede_id = 4 WHERE id IN (7, 8);
UPDATE Hallazgo SET sede_id = 5 WHERE id IN (9, 10);