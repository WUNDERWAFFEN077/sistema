<?php
namespace App\Repositories;

use Core\{Log};
use App\Helpers\{ResponseHelper,AnexGridHelper};
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\{
    SqlViews\ReporteProducto,
    SqlViews\ReporteInventario
};

class ReporteRepository {
    private $reporte_producto;
    private $reporte_inventario;

    public function __construct(){
        $this->reporte_producto = new ReporteProducto();
        $this->reporte_inventario = new ReporteInventario();
    }

    public function ventasPorMes() : string {
        $anexgrid = new AnexGridHelper;

        try {
            $sql = "SELECT
                    YEAR(fecha) anio,
                    MONTH(fecha) mes,
                    SUM(precio*cantidad) total,
                    SUM(costo*cantidad) costo
                    FROM comprobante_detalle d
                    INNER JOIN comprobante c ON (d.comprobante_id = c.id)
                    WHERE c.anulado = 0
                    GROUP BY anio,mes
                    ORDER BY anio DESC,mes DESC
                    LIMIT  $anexgrid->pagina, $anexgrid->limite
            ";
            $result = DB::select($sql);

            $sql = "SELECT COUNT(*) as t FROM 
                (SELECT
                    YEAR(fecha) anio,
                    MONTH(fecha) mes,
                    SUM(precio*cantidad) total,
                    SUM(costo*cantidad) costo
                    FROM comprobante_detalle d
                    INNER JOIN comprobante c ON (d.comprobante_id = c.id)
                    WHERE c.anulado = 0
                    GROUP BY anio,mes) AS tabla
            ";
            $total = DB::select($sql);

            

            return $anexgrid->responde(
                $result,
                $total[0]->t
            );
            
        } catch (Exception $e) {
            Log::error(ReporteRepository::class, $e->getMessage());
        }

        return "";
    }


    public function productoPorMes($y, $m) : string {
        $anexgrid = new AnexGridHelper;

        try {
            $result = $this->reporte_producto->orderBy(
                $anexgrid->columna,
                $anexgrid->columna_orden
            )->where('anio',$y)
            ->where('mes',$m)
            ->skip($anexgrid->pagina)
             ->take($anexgrid->limite)
             ->get();

            return $anexgrid->responde(
                $result,
                $this->reporte_producto->count()
            );
            
        } catch (Exception $e) {
            Log::error(ReporteRepository::class, $e->getMessage());
        }

        return "";
    }


    public function inventariosPorMes($y, $m) : string {
        $anexgrid = new AnexGridHelper;


        try {
            $result = $this->reporte_inventario->orderBy(
                $anexgrid->columna,
                $anexgrid->columna_orden
            )->where('anio',$y)
            ->where('mes',$m)
            ->skip($anexgrid->pagina)
            ->take($anexgrid->limite)
            ->get();

            return $anexgrid->responde(
                $result,
                $this->reporte_inventario->count()
            );
            
        } catch (Exception $e) {
            Log::error(ReporteRepository::class, $e->getMessage());
        }

        return "";
    }


    
    
}