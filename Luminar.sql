DROP DATABASE IF EXISTS LUMINAR;
CREATE DATABASE LUMINAR;
USE LUMINAR;

-- ========================
-- Tabla USUARIO general
-- ========================
CREATE TABLE USUARIO (
                         ID INT PRIMARY KEY AUTO_INCREMENT,
                         NOMBRE VARCHAR(30),
                         APELLIDOS VARCHAR(30),
                         CORREO VARCHAR(50),
                         CONTRA VARCHAR(255),
                         TIPO_USUARIO TINYINT,  -- 1 = Candidato, 2 = Reclutador
                         CHECK (TIPO_USUARIO IN (1, 2))
);

-- ========================
-- Diccionario de TIPO_USUARIO (opcional como catálogo)
-- ========================
CREATE TABLE TIPO_USUARIO (
                              ID TINYINT PRIMARY KEY,
                              DESCRIPCION VARCHAR(20)
);

INSERT INTO TIPO_USUARIO VALUES
                             (1, 'Candidato'),
                             (2, 'Reclutador');

-- ========================
-- Datos específicos de RECLUTADOR
-- ========================
CREATE TABLE RECLUTADOR_INFO (
                                 ID_USUARIO INT PRIMARY KEY,
                                 NOMBRE_EMPRESA VARCHAR(50),
                                 RAZON_SOCIAL VARCHAR(50),
                                 RFC varchar(13),
                                 FOREIGN KEY (ID_USUARIO) REFERENCES USUARIO(ID) on update cascade on delete cascade
);

-- ========================
-- Tabla CV asociada a Candidato
-- ========================
CREATE TABLE CV (
                    ID INT PRIMARY KEY AUTO_INCREMENT,
                    ID_CANDIDATO INT,
                    TITULO VARCHAR(100),
                    FOREIGN KEY (ID_CANDIDATO) REFERENCES USUARIO(ID) on update cascade on delete cascade
);

-- ========================
-- Tabla VACANTE asociada a Reclutador
-- ========================
CREATE TABLE VACANTE (
                         ID INT PRIMARY KEY AUTO_INCREMENT,
                         ID_RECLUTADOR INT,
                         NOMBRE VARCHAR(100),
                         PUESTO VARCHAR(50),
                         SALARIO DECIMAL(10,2),
                         MODALIDAD VARCHAR(30),
                         UBICACION VARCHAR(50),
                         DURACION INT, -- meses
                         FOREIGN KEY (ID_RECLUTADOR) REFERENCES USUARIO(ID) on update cascade on delete cascade
);

-- ========================
-- Tablas de Escolaridad, Habilidades, y Emparejamientos
-- (igual que antes, no cambian por el tipo de usuario)
-- ========================

CREATE TABLE ESCOLARIDAD (
                             ID INT PRIMARY KEY AUTO_INCREMENT,
                             NIVEL VARCHAR(50)
);

CREATE TABLE CV_ESCOLARIDAD (
                                ID_CV INT,
                                ID_ESCOLARIDAD INT,
                                PRIMARY KEY (ID_CV, ID_ESCOLARIDAD),
                                FOREIGN KEY (ID_CV) REFERENCES CV(ID) on update cascade on delete cascade,
                                FOREIGN KEY (ID_ESCOLARIDAD) REFERENCES ESCOLARIDAD(ID) on update cascade on delete cascade
);

CREATE TABLE HABILIDAD (
                           ID INT PRIMARY KEY AUTO_INCREMENT,
                           NOMBRE VARCHAR(50),
                           TIPO ENUM('BLANDA','DURA')
);

CREATE TABLE CV_HABILIDAD (
                              ID_CV INT,
                              ID_HABILIDAD INT,
                              PRIMARY KEY (ID_CV, ID_HABILIDAD),
                              FOREIGN KEY (ID_CV) REFERENCES CV(ID) on update cascade on delete cascade,
                              FOREIGN KEY (ID_HABILIDAD) REFERENCES HABILIDAD(ID) on update cascade on delete cascade
);

CREATE TABLE VACANTE_HABILIDAD (
                                   ID_VACANTE INT,
                                   ID_HABILIDAD INT,
                                   PRIMARY KEY (ID_VACANTE, ID_HABILIDAD),
                                   FOREIGN KEY (ID_VACANTE) REFERENCES VACANTE(ID) on update cascade on delete cascade,
                                   FOREIGN KEY (ID_HABILIDAD) REFERENCES HABILIDAD(ID) on update cascade on delete cascade
);

CREATE TABLE VACANTE_ESCOLARIDAD (
                                     ID_VACANTE INT,
                                     ID_ESCOLARIDAD INT,
                                     PRIMARY KEY (ID_VACANTE, ID_ESCOLARIDAD),
                                     FOREIGN KEY (ID_VACANTE) REFERENCES VACANTE(ID) on update cascade on delete cascade,
                                     FOREIGN KEY (ID_ESCOLARIDAD) REFERENCES ESCOLARIDAD(ID) on update cascade on delete cascade
);

-- ========================
-- Tabla de emparejamiento entre candidatos y vacantes
-- ========================
CREATE TABLE EMPAREJAMIENTO (
                                ID INT PRIMARY KEY AUTO_INCREMENT,
                                ID_CANDIDATO INT,
                                ID_VACANTE INT,
                                COMPATIBILIDAD DECIMAL(5,2),
                                FECHA_MATCH DATE,
                                FOREIGN KEY (ID_CANDIDATO) REFERENCES USUARIO(ID) on update cascade on delete cascade,
                                FOREIGN KEY (ID_VACANTE) REFERENCES VACANTE(ID) on update cascade on delete cascade
);

CREATE TABLE TITULOS_CV (
                            ID INT PRIMARY KEY AUTO_INCREMENT,
                            NOMBRE VARCHAR(100) UNIQUE
);

CREATE TABLE VACANTE_TITULO (
                                ID_VACANTE INT,
                                ID_TITULO INT,
                                PRIMARY KEY (ID_VACANTE, ID_TITULO),
                                FOREIGN KEY (ID_VACANTE) REFERENCES VACANTE(ID) on update cascade on delete cascade,
                                FOREIGN KEY (ID_TITULO) REFERENCES TITULOS_CV(ID) on update cascade on delete cascade
);

INSERT INTO ESCOLARIDAD (NIVEL) VALUES
                                    ('Secundaria'),
                                    ('Preparatoria'),
                                    ('Licenciatura'),
                                    ('Maestría'),
                                    ('Doctorado');

-- Habilidades blandas
INSERT INTO HABILIDAD (NOMBRE, TIPO) VALUES
                                         ('Comunicación Efectiva', 'BLANDA'),
                                         ('Trabajo en Equipo', 'BLANDA'),
                                         ('Pensamiento Crítico', 'BLANDA'),
                                         ('Adaptabilidad', 'BLANDA'),
                                         ('Resolución de Problemas', 'BLANDA'),
                                         ('Liderazgo', 'BLANDA'),
                                         ('Inteligencia Emocional', 'BLANDA'),
                                         ('Gestión de Tiempo', 'BLANDA'),
                                         ('Empatía', 'BLANDA'),
                                         ('Creatividad', 'BLANDA');

-- Habilidades duras
INSERT INTO HABILIDAD (NOMBRE, TIPO) VALUES
                                         ('HTML', 'DURA'),
                                         ('CSS', 'DURA'),
                                         ('PHP', 'DURA'),
                                         ('SQL', 'DURA'),
                                         ('Java', 'DURA'),
                                         ('C', 'DURA'),
                                         ('Python', 'DURA');

INSERT INTO TITULOS_CV (NOMBRE) VALUES
                                    ('Ingeniero de Datos'),
                                    ('Analista de Ciberseguridad'),
                                    ('Desarrollador de Software'),
                                    ('Gestor BD'),
                                    ('IA y Machine Learning');

insert into USUARIO values(default, 'Marin', 'Galvan Diaz', 'maringalvand@gmail.com', '$2y$10$eYIX4y0iDA50x29aTC8Z.eMGZZZolKm.E78u.7AC0P5BS3LD5W5Ya', 1);
insert into USUARIO values(default, 'Juan', 'Perez Prado', 'galvandiazmarin@gmail.com', '$2y$10$82ATzkbmpi3L081.XZNeiuRkwGq3Jxlxw1sF4XqmzAIZ/uLkXPKQi', 2);
insert into RECLUTADOR_INFO values(2, 'Amazon','Amazon México','1234567891123');

select * FROM USUARIO;
select * from RECLUTADOR_INFO;
select * from CV;
select * from HABILIDAD;
select * from VACANTE;
select * from VACANTE_HABILIDAD;
select * from VACANTE_ESCOLARIDAD;
select * from VACANTE_TITULO;

SELECT * FROM USUARIO WHERE CORREO = 'galvandiazmarin@gmail.com';
SELECT * FROM RECLUTADOR_INFO WHERE ID_USUARIO = 2;
