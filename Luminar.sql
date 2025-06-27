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

-- Candidatos (tipo = 1)
insert into USUARIO values(default, 'Lizeth', 'Contreras Delgadillo', 'lizeth.cd@gmail.com', '$2y$10$2dWkHQrUq3IX8eDz5tRciOLdd0x4TE2VMEIb2cn5CMEcXcO8mkTIK', 1); -- contra123
insert into USUARIO values(default, 'Carlos', 'Ramírez Soto', 'c.ramirez@outlook.com', '$2y$10$zGGBx71gMl8QY1QQqJp/6OZB6Up9yTrAC7AZWZMHkU7nW06Y6YiQ2', 1); -- pass123
insert into USUARIO values(default, 'Fernanda', 'Lopez Mejía', 'fer.lopez@yahoo.com', '$2y$10$OmTdb8QmbB9b2MpN2jwIt.oePJPce4En2lo1mrHa9Ov09xAX0zTHC', 1); -- hola2024
insert into USUARIO values(default, 'Hugo', 'Martínez Núñez', 'hugomnz@gmail.com', '$2y$10$Vb4sHeWZzH5U8C9cMQ9S/OofTHIufvOPxyc5OJ2RjybM8ghfUN6t2', 1); -- seguridad
insert into USUARIO values(default, 'Daniela', 'Reyes Pardo', 'danielarp@outlook.com', '$2y$10$zLphZNKAzDAfO7VncCzkUuoVHYKaMT5GGe.MFiAjZTJevCqgX26qC', 1); -- clave456
insert into USUARIO values(default, 'Iván', 'Castro Vela', 'ivancv@yahoo.com', '$2y$10$fEYol3hNwnZ0N9Bz1HoHx.xUNtWnWjoiugxrfQlndkqC3mGSQ1gEq', 1); -- adminpass
insert into USUARIO values(default, 'María', 'Zamora Ruiz', 'm.zamora@hotmail.com', '$2y$10$SR9p5WxR79AKbi9OZ5NUxuLUhU1KfMLvM1LgPYVleUpPVkHuGxHb2', 1); -- prueba789
insert into USUARIO values(default, 'Luis', 'Escobar Limón', 'luisel@gmail.com', '$2y$10$FE14Z0ZAz2NWbpt7v4ZUKOpj5/1frZl/YTbqV5FeV35MyYw5ZLGQG', 1); -- misdatos
insert into USUARIO values(default, 'Andrea', 'Mendoza Córdova', 'andreamc@outlook.com', '$2y$10$3ujA7AGpbdN5JuTW8jYHR.XVt3xgn7uUEm.5dArCM8IlJK4V7yUra', 1); -- cv2025
insert into USUARIO values(default, 'Roberto', 'Jiménez Oropeza', 'robertojo@yahoo.com', '$2y$10$sYF5Gi5FlfReZwnqYAZ/gOkfVFeUqLHiN/WIGx7wzj2N3XLyq7HyG', 1); -- roberpass

-- Reclutadores (tipo = 2)
insert into USUARIO values(default, 'Alexander', 'Maldonado Hernández', 'alexander.mh@gmail.com', '$2y$10$2edMyIRsnzoUtFvGO7qC8OnTmgx6BbbNm/O0clHHi/huhKcQWfhsm', 2); -- alexpass
insert into USUARIO values(default, 'Paula', 'Mendoza Soto', 'paula.mendoza@outlook.com', '$2y$10$tOf7tkPCXpgVj8ckszC/UOQ3KD2xmsuVWgjMqxV7XumJDNkmHgOE6', 2); -- paula456
insert into USUARIO values(default, 'Raúl', 'Salinas Márquez', 'raul.salinas@yahoo.com', '$2y$10$emRB9ezNhwk37DNObe1U7edQJ5pmrWcUdLa0K8DDmOLthcBz.zQtK', 2); -- reclutador
insert into USUARIO values(default, 'Beatriz', 'González Vega', 'beatriz.gv@gmail.com', '$2y$10$2FKpY9YRCgcMKGO5PPb7WOTvhTg2OMLzkcRwh8jBUI3L8TkfdFbTC', 2); -- empresa2025
insert into USUARIO values(default, 'Jorge', 'López Rivera', 'jorge.lopez@outlook.com', '$2y$10$z1Zx7lOYFzRUhxTtG.qNUOyxIsMPiL5gfkOcG2kTJ9cSpc2fuvQ7K', 2); -- gerente
insert into USUARIO values(default, 'Diana', 'Ramos Pineda', 'diana.rp@yahoo.com', '$2y$10$t16rI7ZJ/Ofn6ZB7DkY6R.7sdfQaGzRBDnNu5pTKxQGBfYe.W8BhK', 2); -- dia789
insert into USUARIO values(default, 'Carlos', 'Navarrete Díaz', 'carlos.nd@gmail.com', '$2y$10$yWMsUkKnpZqTKCIwtNWvUuvsK7uHGNyU4x/vBMB5FO/1Fb7GgLy8q', 2); -- pasito
insert into USUARIO values(default, 'Adriana', 'Salas Quintero', 'adrisq@outlook.com', '$2y$10$SK6sJkxXr7he7PQ4WJcFjeYgYZRm7zqHJDbqDU0ETTyV1N88eOOpq', 2); -- miempresa
insert into USUARIO values(default, 'Eduardo', 'Carranza Jasso', 'eduardo.cj@hotmail.com', '$2y$10$VzWp4sTTDe58f6zLFMZIOOo03GfUX9rAbx.0UXKlvzqMVUK8tVqIm', 2); -- contratacion
insert into USUARIO values(default, 'Lucía', 'Figueroa Márquez', 'lucia.figmar@yahoo.com', '$2y$10$B.KHavzHifArwWjKeENrgOBZf9KX7vKp9np7liP2NN6r9vEu7AJ3O', 2); -- rrhh2024

insert into RECLUTADOR_INFO values(11, 'Amazon', 'Amazon México', '1234567891123');
insert into RECLUTADOR_INFO values(12, 'Google', 'Google Inc.', '9876543210001');
insert into RECLUTADOR_INFO values(13, 'Microsoft', 'Microsoft LATAM', '5551234567890');
insert into RECLUTADOR_INFO values(14, 'Oracle', 'Oracle México', '1111222233334');
insert into RECLUTADOR_INFO values(15, 'Tesla', 'Tesla Corporation', '2222333344445');
insert into RECLUTADOR_INFO values(16, 'IBM', 'IBM Services', '3333444455556');
insert into RECLUTADOR_INFO values(17, 'Meta', 'Meta Platforms', '4444555566667');
insert into RECLUTADOR_INFO values(18, 'Intel', 'Intel México', '5555666677778');
insert into RECLUTADOR_INFO values(19, 'Salesforce', 'Salesforce LATAM', '6666777788889');
insert into RECLUTADOR_INFO values(20, 'Cisco', 'Cisco Systems', '7777888899990');

select * FROM USUARIO;
select * from RECLUTADOR_INFO;
select * from CV;
select * from CV_HABILIDAD;
select * from HABILIDAD;
select * from VACANTE;
select * from VACANTE_HABILIDAD;
select * from VACANTE_ESCOLARIDAD;
select * from VACANTE_TITULO;

SELECT * FROM USUARIO WHERE CORREO = 'galvandiazmarin@gmail.com';
SELECT * FROM RECLUTADOR_INFO WHERE ID_USUARIO = 2;

-- Asegúrate de ejecutar esto después de haber cargado los datos base

-- Inserta 1 CV por cada candidato
INSERT INTO CV (ID_CANDIDATO, TITULO) VALUES
                                          (1, 'Desarrollador de Software'),
                                          (2, 'Gestor BD'),
                                          (3, 'IA y Machine Learning'),
                                          (4, 'Ingeniero de Datos'),
                                          (5, 'Analista de Ciberseguridad'),
                                          (6, 'Desarrollador de Software'),
                                          (7, 'Gestor BD'),
                                          (8, 'Ingeniero de Datos'),
                                          (9, 'IA y Machine Learning'),
                                          (10, 'Desarrollador de Software');

-- Todos tienen LICENCIATURA (ID_ESCOLARIDAD = 3)
-- Recupera IDs autoincrementales generados en la tabla CV
-- Asumimos que los IDs generados van del 1 al 10 (en orden)
INSERT INTO CV_ESCOLARIDAD (ID_CV, ID_ESCOLARIDAD) VALUES
                                                       (1, 3), (2, 3), (3, 3), (4, 3), (5, 3),
                                                       (6, 3), (7, 3), (8, 3), (9, 3), (10, 3);

-- Asignamos habilidades aleatorias (máx 3 por CV)
-- ID de habilidades según tu inserción (1 a 17)
INSERT INTO CV_HABILIDAD (ID_CV, ID_HABILIDAD) VALUES
                                                   (1, 3), (1, 11), (1, 15),
                                                   (2, 4), (2, 12),
                                                   (3, 1), (3, 14), (3, 17),
                                                   (4, 6),
                                                   (5, 2), (5, 11), (5, 16),
                                                   (6, 5), (6, 13),
                                                   (7, 7), (7, 11), (7, 15),
                                                   (8, 8), (8, 12),
                                                   (9, 10), (9, 13), (9, 17),
                                                   (10, 2), (10, 14);

SELECT
    U.NOMBRE,
    CV.TITULO,
    MAX(E.NIVEL) AS ESCOLARIDAD,
    GROUP_CONCAT(H.NOMBRE SEPARATOR ', ') AS HABILIDADES
FROM USUARIO U
         JOIN CV ON U.ID = CV.ID_CANDIDATO
         JOIN CV_ESCOLARIDAD CE ON CV.ID = CE.ID_CV
         JOIN ESCOLARIDAD E ON CE.ID_ESCOLARIDAD = E.ID
         LEFT JOIN CV_HABILIDAD CH ON CH.ID_CV = CV.ID
         LEFT JOIN HABILIDAD H ON CH.ID_HABILIDAD = H.ID
GROUP BY U.ID, CV.ID;