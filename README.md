Esta es una pequeña Agenda creada con Apache, PHP y SQL Server, con la ayuda de Claude, el mismo tiene un CRUD basico. 
La finalidad del mismo es implementar el modelo MVC de forma nativa,a fin de poder entender MVC.
El diseño de la BD es:
--
-- Este Script es de Claude
--
USE agenda_db;
GO

-- ============================================================
-- TABLA: categorias
-- Un contacto pertenece a una categoría (Amigo, Trabajo, etc)
-- ============================================================
CREATE TABLE categorias (
    id       INT IDENTITY(1,1) PRIMARY KEY,
    nombre   VARCHAR(50)  NOT NULL,
    color    VARCHAR(7)   NOT NULL DEFAULT '#6c757d'  -- color HEX
);
GO

-- ============================================================
-- TABLA: contactos
-- Datos principales del contacto
-- ============================================================
CREATE TABLE contactos (
    id           INT IDENTITY(1,1) PRIMARY KEY,
    nombre       VARCHAR(100) NOT NULL,
    apellido     VARCHAR(100) NOT NULL,
    email        VARCHAR(150) NULL,
    direccion    VARCHAR(250) NULL,
    id_categoria INT          NULL,
    fecha_alta   DATETIME     NOT NULL DEFAULT GETDATE(),

    CONSTRAINT fk_contacto_categoria
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id)
);
GO

-- ============================================================
-- TABLA: telefonos
-- Un contacto puede tener MÚLTIPLES teléfonos
-- Relación: 1 contacto → muchos teléfonos
-- ============================================================
CREATE TABLE telefonos (
    id           INT IDENTITY(1,1) PRIMARY KEY,
    id_contacto  INT          NOT NULL,
    numero       VARCHAR(20)  NOT NULL,
    tipo         VARCHAR(20)  NOT NULL DEFAULT 'Personal',
    -- tipo: Personal | Trabajo | Casa | Otro

    CONSTRAINT fk_telefono_contacto
        FOREIGN KEY (id_contacto)
        REFERENCES contactos(id)
        ON DELETE CASCADE  -- al borrar contacto, borra sus teléfonos
);
GO

-- ============================================================
-- DATOS DE PRUEBA
-- ============================================================

-- Categorías
INSERT INTO categorias (nombre, color) VALUES
('Amigos',    '#28a745'),
('Trabajo',   '#007bff'),
('Familia',   '#dc3545'),
('Otros',     '#6c757d');
GO

-- Contactos
INSERT INTO contactos (nombre, apellido, email, direccion, id_categoria) VALUES
('Juan',  'Pérez',    'juan@email.com',  'Av. Principal 123', 1),
('María', 'González', 'maria@email.com', 'Calle 5 de Mayo 45', 2),
('Carlos','Rodríguez','carlos@email.com','Jr. Los Pinos 78',  3);
GO

-- Teléfonos (múltiples por contacto)
INSERT INTO telefonos (id_contacto, numero, tipo) VALUES
(1, '555-1001', 'Personal'),
(1, '555-1002', 'Trabajo'),   -- Juan tiene 2 teléfonos
(2, '555-2001', 'Personal'),
(2, '555-2002', 'Casa'),      -- María tiene 2 teléfonos
(2, '555-2003', 'Trabajo'),   -- María tiene 3 teléfonos
(3, '555-3001', 'Personal');
GO
