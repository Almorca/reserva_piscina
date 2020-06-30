
CREATE TABLE `franjas` (
 `dia` tinyint(3) unsigned NOT NULL,
 `mes` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
 `franja` tinyint(3) unsigned NOT NULL,
 `aforo` tinyint(3) unsigned NOT NULL,
 `num_mes` tinyint(3) unsigned DEFAULT NULL,
 `estado_franja` int(11) DEFAULT NULL,
 PRIMARY KEY (`dia`,`mes`,`franja`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci


CREATE TABLE `pisos` (
 `piso` varchar(64) COLLATE utf8_spanish_ci NOT NULL,
 `password` varchar(64) COLLATE utf8_spanish_ci NOT NULL,
 `email` varchar(320) COLLATE utf8_spanish_ci DEFAULT NULL,
 PRIMARY KEY (`piso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci


CREATE TABLE `reservas_solicitadas` (
 `dia` tinyint(3) unsigned NOT NULL,
 `mes` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
 `franja` tinyint(3) unsigned NOT NULL,
 `piso` varchar(64) COLLATE utf8_spanish_ci NOT NULL,
 `estado` tinyint(3) unsigned DEFAULT NULL,
 `personas` tinyint(3) unsigned DEFAULT NULL,
 PRIMARY KEY (`dia`,`mes`,`franja`,`piso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci


-- Datos de ejemplo
INSERT INTO `franjas` (`dia`, `mes`, `franja`, `aforo`, `num_mes`, `estado_franja`) VALUES
(20, 'junio', 1, 5, 6, NULL),
(20, 'junio', 2, 5, 6, NULL);


INSERT INTO `pisos`(`piso`, `password`) VALUES ('A-1-1','$2y$10$pJJ/qdmNiYSf3E8FyyhBlusYZHD9afacjDTo78G4gr.ZH/5mHjMzm'); -- Contraseña 123456
