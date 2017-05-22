SELECT 
archivo.fecha as FECHA,
(CASE archivo.mes
 WHEN "1" THEN "Enero"
 WHEN "2" THEN "Febrero"
 WHEN "3" THEN "Marzo"
 WHEN "4" THEN "Abril"
 WHEN "5" THEN "Mayo"
 WHEN "6" THEN "Junio"
 WHEN "7" THEN "Julio"
 WHEN "8" THEN "Agosto"
 WHEN "9" THEN "Septiembre"
 WHEN "O" THEN "Octubre"
 WHEN "N" THEN "Noviembre"
 WHEN "D" THEN "Diciembre"
 END) as MES,
concat('20',archivo.anio) AS 'AÑO',
cuenta.nombres as USUARIO
FROM TBlArchivo as archivo
INNER JOIN TBlcuenta as cuenta
ON archivo.idcuenta = cuenta.idCuenta
INNER JOIN TblDistribuidor as distribuidor
ON cuenta.idDistribuidor = distribuidor.idDistribuidor
WHERE distribuidor.codigo = 'A'



DELIMITER $$
DROP PROCEDURE IF EXISTS insertapaises;
CREATE PROCEDURE `insertapaises`(codigo varchar(4), nombre varchar(100), bandera varchar(100), continente varchar(100))
BEGIN
declare existe int;
set existe = (select count(*) from paises where codigopais = codigo);
if existe =0
then
select existe;
insert into paises (codigopais, nombre, bandera, fecha_creacion, continente)
values (codigo, nombre, bandera, now(), continente);
else
SELECT 'Pais ya existe, se actualizarán los datos';
UPDATE paises set nombre = nombre, bandera=bandera where codigopais=codigo;
END if;
end$$




DELIMITER $$
    DROP FUNCTION IF EXISTS `SQL_TRY_CATCH`$$
    CREATE FUNCTION `SQL_TRY_CATCH`() RETURNS INT
    BEGIN
        DECLARE `_rollback` BOOL DEFAULT 0;

        DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;
        START TRANSACTION;

        Insert into test(val) VALUES('hola');

        IF `_rollback` THEN
            ROLLBACK;
        ELSE
            COMMIT;
        END IF;
    END$$
    DELIMITER ;
    
    
    
    DELIMITER $$
DROP PROCEDURE IF EXISTS `SQL_TRY_CATCH`;$$

CREATE PROCEDURE `SQL_TRY_CATCH`()
BEGIN
    DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;
    START TRANSACTION;
    UPDATE entities SET `slug`='servers' WHERE `slug`='devices';
    IF `_rollback` THEN
        ROLLBACK;
    ELSE
        COMMIT;
    END IF;
END$$

DELIMITER ;