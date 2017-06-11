CREATE VIEW reporte_producto AS SELECT
c.id,
YEAR(fecha) anio,
MONTH(fecha) mes,
p.nombre producto,
d.cantidad,
d.costo,
d.total,
(d.total-d.costo) utilidad
FROM comprobante_detalle d
INNER JOIN comprobante c ON (d.comprobante_id = c.id)
INNER JOIN producto p ON (d.producto_id = p.id)
WHERE c.anulado = 0 ;