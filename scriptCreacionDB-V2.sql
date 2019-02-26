DROP DATABASE IF EXISTS unaWebDB;
CREATE DATABASE unaWebDB;
USE unaWebDB;

DROP TABLE IF EXISTS unaSecciones;
CREATE TABLE unaSecciones(
	idSeccion 		INT AUTO_INCREMENT PRIMARY KEY,
    nombreSeccion 	NVARCHAR(45),
    descripcion		NVARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS unaUsuarios;
CREATE TABLE unaUsuarios(
	idUsuario 		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    apodo 			NVARCHAR(25) NOT NULL,
    contrasenia 	NVARCHAR(255) NOT NULL,
    mail 			NVARCHAR(50) NOT NULL,
    rol 			NVARCHAR(4),
    fechaIngreso 	DATETIME,
    estadoCuenta 	NVARCHAR(7),
    dirImg			NVARCHAR(45),
    token			NVARCHAR(35),
    redSocial1		NVARCHAR(140),
    redSocial2		NVARCHAR(140),
    redSocial3		NVARCHAR(140),
    redSocial4		NVARCHAR(140),
    CONSTRAINT constraintUsuario UNIQUE(apodo)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS unaTemas;
CREATE TABLE unaTemas(
	idTema 				INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    titulo 				NVARCHAR(80),
    idSeccion 			INT,
    idUsuario 			INT,
    palabraClave1 		NVARCHAR(12),
    palabraClave2 		NVARCHAR(12),
    palabraClave3 		NVARCHAR(12),
    fechaCreacion 		DATETIME,
    comentarioInicial 	TEXT NOT NULL,
    FOREIGN KEY (idSeccion) REFERENCES unaSecciones (idSeccion) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (idUsuario) REFERENCES unaUsuarios (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
    
DROP TABLE IF EXISTS unaCursos;
CREATE TABLE unaCursos(
	idCurso 		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombreMateria 	NVARCHAR(100) NOT NULL,
    nombreCatedra 	NVARCHAR(60) NOT NULL,
    sede 			NVARCHAR(40) NOT NULL,
    codigo			NVARCHAR(15),
    horario 		NVARCHAR (40),
    fechaCreacion	DATETIME
    )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS unaComentarios;
CREATE TABLE unaComentarios(
	idComentario 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    contenido 		TEXT NOT NULL,
    fechaHora 		DATETIME,
    idTema 			INT,
    idUsuario 		INT,
    FOREIGN KEY (idTema) REFERENCES unaTemas (idTema) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (idUsuario) REFERENCES unaUsuarios (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS unaComentarioXcurso;
CREATE TABLE unaComentarioXcurso(
	idComentario 	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    contenido 		TEXT NOT NULL,
    fechaHora 		DATETIME,
    idCurso			INT,
    idUsuario 		INT,
    FOREIGN KEY (idUsuario) REFERENCES unaUsuarios (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (idCurso) REFERENCES unaCursos (idCurso) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS unaComentarioXcatedra;
CREATE TABLE unaComentarioXcatedra(
	idComentario	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    contenido		TEXT NOT NULL,
    fechaHora		DATETIME,
    idUsuario		INT,
    idCatedra		INT,
    FOREIGN KEY (idUsuario) REFERENCES unaUsuarios(idUsuario) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (idUsuario) REFERENCES unaUsuarios(idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS unaCatedra;
CREATE TABLE unaCatedra(
	idCatedra	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    catedra		NVARCHAR(60),
    materia		NVARCHAR(100),
    profesores	NVARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS unaApuntes;
CREATE TABLE unaApuntes(
	idApunte	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    autores		NVARCHAR(100),
    titulo		NVARCHAR(100),
    materia		NVARCHAR(120),
    fechaSubida	DATETIME,
    usuario		INT,
    dirurl		NVARCHAR(170),
    FOREIGN KEY (usuario) REFERENCES unaUsuarios(idUsuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=UTF8_spanish_ci;

CREATE USER 'unAdminDB'@'%' IDENTIFIED BY 'u+e-123*clave';
GRANT DELETE,INSERT,SELECT,UPDATE ON unaWebDB.* TO 'unAdminDB'@'%';

INSERT INTO unaWebDB.unaUsuarios (apodo,contrasenia,mail,rol,estadoCuenta,fechaIngreso) VALUES('ADMIN','$2y$10$lOBm99ma5W7ExgFjBpK/AOJst6e9f9FuHGB25e7s7ifDqFaNt86vq','ces1406@gmail.com','ADMI','HABILIT',CURRENT_TIMESTAMP());
#apodo:ADMIN pass:ADM123 es el usuario inicial administrador
insert into unaWebDB.unaSecciones (nombreSeccion,descripcion) values ('Agenda','calendario y fechas de actividades y eventos');
insert into unaWebDB.unaSecciones (nombreSeccion,descripcion) values ('Temas generales','temas que no están en otras secciones');
insert into unaWebDB.unaSecciones (nombreSeccion,descripcion) values ('Cursos por cátedras','canal de comunicación organizado por los distintos cursos de cada una de las materias');
insert into unaWebDB.unaSecciones (nombreSeccion,descripcion) values ('Compra, venta e intercambio de materiales','compra, venta y canje de distintos materiales del rubro artístico');
insert into unaWebDB.unaSecciones (nombreSeccion,descripcion) values ('Exposiciones','avisos de actividades y exposiciones');
insert into unaWebDB.unaSecciones (nombreSeccion,descripcion) values ('Opiniones de cátedras y profesores','busca y deja opiniones sobre distintas materias, cátedras y profesores');
insert into unaWebDB.unaSecciones (nombreSeccion,descripcion) values ('Política interna','temas sobre la política interna de la UNA');
insert into unaWebDB.unaSecciones (nombreSeccion,descripcion) values ('Info académica y trámites','cuestiones académicas, dudas sobre trámites, inscripciones, certificados, etc.');
insert into unaWebDB.unaSecciones (nombreSeccion,descripcion) values ('Links y sitios relacionados','links a distintas paginas relacionadas con la UNA');
insert into unaWebDB.unaSecciones (nombreSeccion,descripcion) values ('Apuntes','aportes y búsquedas de apuntes');
