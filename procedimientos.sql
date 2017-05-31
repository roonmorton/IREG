
DROP PROCEDURE IF EXISTS PRUEBA;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `prueba`(
    IN _usuario VARCHAR(45),
    IN _departamento VARCHAR(75),
    IN _municipio varchar(75),
    IN _codigoPostal varchar(20),
    IN _aldea varchar(75),
    IN _canton varchar(75),
    IN _caserio varchar(75),
    IN _id_Usuario varchar(75),
    IN _nombreUsuario varchar(75),
    IN _calle varchar(75),
    IN _numeroCalle varchar(75),
    IN _piso varchar(75),
    IN _unidad varchar(10),
    IN _telefono varchar(10),
    IN _potencia decimal(16,2),
    IN _planFacturacion varchar(15),
    IN _tarifa varchar(5),
    
    IN _noMedidor1 varchar(40),
    IN _tipoMedidor1 varchar(35),
    IN _FechaColocacion1 varchar(75),
    IN _noMedidor2 varchar(40),
    IN _tipoMedidor2 varchar(35),
    IN _FechaColocacion2  varchar(75),
    IN _noMedidor3 varchar(40),
    IN _tipoMedidor3 varchar(35),
    IN _FechaColocacion3 varchar(75),
    
    IN _calidaServicio varchar(2),
    IN _anio varchar(4),
    IN _mes varchar(2),
    IN _nombreTabla varchar(75),
    
    IN _tipoRegistro varchar(2),
    

    OUT _msg varchar(75),
    OUT _registro varchar(75),
    IN _idArchivo varchar(60) 
)
BEGIN

    DECLARE pidPais int DEFAULT 0;
    DECLARE pidDepartamento int DEFAULT 0;
        DECLARE pidMunicipio int DEFAULT 0;
    	DECLARE pidAldea int DEFAULT 0;
        DECLARE pidCanton int DEFAULT 0;
        DECLARE pidCaserio int DEFAULT 0;
        DECLARE pidMunicipioAldea int DEFAULT 0;
        DECLARE pidPotencia int DEFAULT 0;
        DECLARE pidPlanFacturacion int DEFAULT 0;
        DECLARE pidTarifa int DEFAULT 0;
        DECLARE pidMedidor int DEFAULT 0;
        DECLARE pidArchivo int DEFAULT 0;
        DECLARE pidUsuario int DEFAULT 0;
        DECLARE pidRegistro int DEFAULT 0;

        SET pidPais = (SELECT pais.idPais from tblPais as pais 
                       INNER JOIN tblDistribuidor as distribuidor 
                       ON pais.idPais = distribuidor.idPais 
                       INNER JOIN TblCuenta as cuenta 
                       ON cuenta.idDistribuidor = distribuidor.idDistribuidor 
                       WHERE cuenta.nombreUsuario = _Usuario);

        
        IF pidPais > 0 THEN

            
            IF EXISTS(select 1 from tbldepartamento where nombre = _Departamento and idPais = pidPais) THEN
                SET pidDepartamento = (select idDepartamento from tbldepartamento WHERE nombre = _Departamento and idPais = pidPais);
            ELSE
                INSERT INTO tbldepartamento(nombre, idPais) VALUES(_Departamento,pidPais);
                SET pidDepartamento = (select LAST_INSERT_ID());
            END IF;

            
            IF EXISTS(SELECT 1 FROM tblmunicipio WHERE nombre = _municipio AND idDepartamento = pidDepartamento AND codigoPostal = _codigoPostal) THEN
                SET pidMunicipio = (SELECT idMunicipio FROM tblmunicipio WHERE nombre = _municipio AND idDepartamento = pidDepartamento AND codigoPostal = _codigoPostal);
            ELSE
                    INSERT INTO tblmunicipio(nombre,idDepartamento,codigoPostal) VALUES(_municipio,pidDepartamento,_codigoPostal);
                    SET pidMunicipio = (SELECT LAST_INSERT_ID());
            END if;

			
            IF EXISTS(SELECT 1 FROM tblaldea WHERE nombre = _aldea) THEN
            	SET pidAldea = (SELECT idAldea FROM tblaldea WHERE nombre = _aldea);
            ELSE
                    INSERT INTO tblaldea(nombre) VALUES(_aldea);
                    SET pidAldea = (SELECT LAST_INSERT_ID());
            END IF;

            
            IF EXISTS( SELECT 1 FROM tblcantonzona WHERE nombre = _canton) THEN
            	SET pidCanton = (SELECT idCantonZona FROM tblcantonzona WHERE nombre = _canton);
            ELSE
            	INSERT INTO tblcantonzona(nombre) VALUES(_canton);
                SET pidCanton = (SELECT LAST_INSERT_ID());
            END IF;


            
            IF EXISTS( SELECT 1 FROM tblcaserio WHERE nombre = _caserio) THEN
            	SET pidCaserio =  (SELECT idCaserio FROM tblcaserio WHERE nombre = _caserio);
            ELSE
            	INSERT INTO tblcaserio(nombre) VALUES(_caserio);
                SET pidCaserio = (SELECT LAST_INSERT_ID());
            END IF;

            
            IF EXISTS(SELECT 1 from tblmunicipiohasaldea where idMunicipio = pidMunicipio AND idAldea = pidAldea AND idCantonZona = pidCanton AND idCaserio = pidCaserio) THEN
            	SET pidMunicipioAldea = (SELECT idMunicipiohasAldea from tblmunicipiohasaldea where idMunicipio = pidMunicipio AND idAldea = pidAldea AND idCantonZona = pidCanton AND idCaserio = pidCaserio);
            ELSE
                INSERT INTO tblmunicipiohasaldea(idMunicipio,idAldea,idCantonZona,idCaserio) VALUES(pidMunicipio,pidAldea,pidCanton,pidCaserio);
                SET pidMunicipioAldea = (SELECT LAST_INSERT_ID());
            END if;


            
          IF EXISTS(SELECT 1 FROM tblusuario WHERE nombres = _nombreUsuario and calle =_calle and numero = _numeroCalle and piso = _piso and unidad = _unidad and telefono = _telefono and idMunicipioHasAldea = pidMunicipioAldea) THEN
            SET pidUsuario = (SELECT idU FROM tblusuario WHERE nombres = _nombreUsuario and calle =_calle and numero = _numeroCalle and piso = _piso and unidad = _unidad and telefono = _telefono and idMunicipioHasAldea = pidMunicipioAldea);
          ELSE
          	INSERT INTO tblusuario(idUsuario,idMunicipioHasAldea,nombres,calle,numero,piso,unidad,telefono) VALUES(_id_usuario,pidMunicipioAldea,_nombreUsuario,_calle,_numeroCalle,_piso,_unidad,_telefono);
            SET pidUsuario = (SELECT LAST_INSERT_ID());
          END if;


          IF EXISTS(SELECT 1 FROM tblpotencia WHERE cantidad = (CONVERT(_potencia,DECIMAL(10,2))) ) THEN
          	SET pidPotencia = (SELECT idPotencia FROM tblpotencia WHERE cantidad = (CONVERT(_potencia,DECIMAL(10,2))) );
          ELSE
          	INSERT INTO tblpotencia(cantidad) values( (CONVERT(_potencia,DECIMAL(10,2))) );
            SET pidPotencia = (SELECT LAST_INSERT_ID());
          END IF;



            
          IF EXISTS(SELECT 1 FROM tblplanfacturacion WHERE codigo = _planFacturacion) THEN
          	SET pidPlanFacturacion = (SELECT idPlanFacturacion FROM tblplanfacturacion WHERE codigo = _planFacturacion);
          ELSE
          	INSERT INTO tblplanfacturacion(codigo) VALUES(_planFacturacion);
            SET pidPlanFacturacion = (SELECT LAST_INSERT_ID());
          END IF;


            
          IF EXISTS(SELECT 1 FROM tbltarifa WHERE siglas = _tarifa ) THEN
          	SET pidTarifa = (SELECT idTarifa FROM tbltarifa WHERE siglas = _tarifa);
          ELSE
          	INSERT into tbltarifa(siglas) VALUES(_tarifa);
            SET pidTarifa = (SELECT LAST_INSERT_ID());
          END if;


        

        SET pidArchivo = _idArchivo;
        IF NOT EXISTS(SELECT 1 FROM tblArchivo WHERE idArchivo = pidArchivo) THEN
            INSERT INTO tblArchivo(fecha,calidadServicio,anio,mes,nombreTabla,idCuenta) values(NOW(),_calidaServicio, _anio, _mes, _nombreTabla,(select idCuenta from TblCuenta where nombreUsuario = _usuario ));
            SET pidArchivo = (SELECT LAST_INSERT_ID());
        END IF;


		set _registro = pidArchivo;
        
        
        INSERT INTO tblregistro(tipoRegistro,idArchivo,idUsuario,idTarifa,idPlanFacturacion,idPotencia) VALUES(_tipoRegistro,pidArchivo,pidUsuario,pidTarifa,pidPlanFacturacion,pidPotencia);
		        SET pidRegistro = (SELECT LAST_INSERT_ID());
        
        
        
        
        
        IF EXISTS(SELECT 1 FROM tblMedidor WHERE descripcion = _tipoMedidor1) THEN
            SET pidMedidor = (SELECT idMedidor FROM tblMedidor WHERE descripcion = _tipoMedidor1);
        ELSE
            INSERT INTO tblMedidor(descripcion) VALUES(_tipoMedidor1);
            SET pidMedidor = (SELECT LAST_INSERT_ID());
        END IF;
        
        
        INSERT INTO tblRegistroHasMedidor(idRegistro,fechaColocacion,idMedidor,noMedidor) VALUES(pidRegistro,_fechaColocacion1,pidMedidor,_noMedidor1);
        
        
        
        IF EXISTS(SELECT 1 FROM tblMedidor WHERE descripcion = _tipoMedidor2) THEN
            SET pidMedidor = (SELECT idMedidor FROM tblMedidor WHERE descripcion = _tipoMedidor2);
        ELSE
            INSERT INTO tblMedidor(descripcion) VALUES(_tipoMedidor2);
            SET pidMedidor = (SELECT LAST_INSERT_ID());
        END IF;
        
        
           INSERT INTO tblRegistroHasMedidor(idRegistro,fechaColocacion,idMedidor,noMedidor) VALUES(pidRegistro,_fechaColocacion2,pidMedidor,_noMedidor2);
        
        
        
        
        IF EXISTS(SELECT 1 FROM tblMedidor WHERE descripcion = _tipoMedidor3) THEN
            SET pidMedidor = (SELECT idMedidor FROM tblMedidor WHERE descripcion = _tipoMedidor3);
        ELSE
            INSERT INTO tblMedidor(descripcion) VALUES(_tipoMedidor3);
            SET pidMedidor = (SELECT LAST_INSERT_ID());
        END IF;
        
        
                   INSERT INTO tblRegistroHasMedidor(idRegistro,fechaColocacion,idMedidor,noMedidor) VALUES(pidRegistro,_fechaColocacion3,pidMedidor,_noMedidor3);

            
            set _registro = pidArchivo;
         set _msg = "FIN";

        ELSE
            SET _msg = "error";
        END IF;

END$$
DELIMITER ;





SELECT usuario.idUsuario, tarifa.siglas as Tarifa, registro.tipoRegistro as TIPOREGISTRO,
usuario.nombres as NOMBRE, usuario.calle , usuario.numero, usuario.piso, usuario.unidad,
usuario.telefono, municipio.codigoPostal, departamento.nombre as departamento, municipio.nombre as municipio,
aldea.nombre as aldea, cantonZona.nombre as canton, caserio.nombre as CASERIO, potencia.cantidad as potencia,
facturacion.codigo as PLANFACTURACION
FROM tblarchivo as archivo
INNER JOIN tblregistro AS registro
ON archivo.idArchivo = registro.idArchivo
INNER JOIN tblTarifa as tarifa
ON tarifa.idTarifa = registro.idTarifa
INNER JOIN tblplanfacturacion facturacion
ON registro.idPlanFacturacion = facturacion.idPlanFacturacion
INNER JOIN tblpotencia as potencia
ON registro.idPotencia = potencia.idPotencia
INNER JOIN tblusuario AS usuario
ON registro.idUsuario = usuario.idU
INNER JOIN tblmunicipiohasaldea as munAldea
ON usuario.idMunicipioHasAldea = munAldea.idMunicipioHasAldea
INNER JOIN tblcantonzona As cantonZona
ON munaldea.idCantonZona = cantonZona.idCantonZona
INNER JOIN tblcaserio as caserio
ON munaldea.idCaserio = caserio.idcaserio
INNER JOIN tblaldea as aldea 
ON munaldea.idAldea = aldea.idAldea
INNER JOIN tblmunicipio as municipio
ON munaldea.idMunicipio = municipio.idMunicipio
INNER JOIN tbldepartamento AS departamento
ON municipio.idDepartamento = departamento.idDepartamento
WHERE archivo.idArchivo = 15



delete from tblregistrohasmedidor;
delete from tblmedidor;
delete from tblregistro;
delete from tblarchivo;
delete from tbltarifa;
delete from tblpotencia;
delete from tblplanfacturacion;
delete from tblusuario;
delete from tblmunicipiohasaldea;
delete from tblcantonzona;
delete from tblcaserio;
delete from tblaldea;
delete from tblmunicipio;
delete from tbldepartamento;

SELECT usuario.idUsuario, tarifa.siglas as Tarifa, registro.tipoRegistro as TIPOREGISTRO, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 0,1) as NroMedidor1, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 0,1) as TipoMedidor1, (select tblRegistroHasMedidor.fechaColocacion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 0,1) as FechaColocacion1, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 1,1) as NroMedidor2, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 1,1) as TipoMedidor2, (select  tblRegistroHasMedidor.fechaColocacion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 1,1) as FechaColocacion2, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 2,1) as NroMedidor3, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 2,1) as TipoMedidor3, (select  tblRegistroHasMedidor.fechaColocacion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 2,1) as FechaColocacion3, usuario.nombres as NOMBRE, usuario.calle , usuario.numero, usuario.piso, usuario.unidad, usuario.telefono, municipio.codigoPostal, departamento.nombre as departamento, municipio.nombre as municipio, aldea.nombre as aldea, cantonZona.nombre as canton, caserio.nombre as CASERIO, potencia.cantidad as potencia, facturacion.codigo as PLANFACTURACION FROM tblarchivo as archivo INNER JOIN tblregistro AS registro ON archivo.idArchivo = registro.idArchivo INNER JOIN tblTarifa as tarifa ON tarifa.idTarifa = registro.idTarifa INNER JOIN tblplanfacturacion facturacion ON registro.idPlanFacturacion = facturacion.idPlanFacturacion INNER JOIN tblpotencia as potencia ON registro.idPotencia = potencia.idPotencia INNER JOIN tblusuario AS usuario ON registro.idUsuario = usuario.idU INNER JOIN tblmunicipiohasaldea as munAldea ON usuario.idMunicipioHasAldea = munAldea.idMunicipioHasAldea INNER JOIN tblcantonzona As cantonZona ON munaldea.idCantonZona = cantonZona.idCantonZona INNER JOIN tblcaserio as caserio ON munaldea.idCaserio = caserio.idcaserio INNER JOIN tblaldea as aldea  ON munaldea.idAldea = aldea.idAldea INNER JOIN tblmunicipio as municipio ON munaldea.idMunicipio = municipio.idMunicipio INNER JOIN tbldepartamento AS departamento ON municipio.idDepartamento = departamento.idDepartamento WHERE archivo.idArchivo = 9 