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