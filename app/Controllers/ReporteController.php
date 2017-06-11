<?php
namespace App\Controllers;

use App\Repositories\{ReporteRepository};
use Core\{Controller};

class ReporteController extends Controller {
    private $reporteRepo;

    public function __construct() {
        parent::__construct();
        $this->reporteRepo = new ReporteRepository();
    }

    public function getVentas() {
        return $this->render('reporte/ventas.twig', [
            'title' => 'Reporte',
            'mainmenu' => 'reportes',
            'submenu' => 'ventas',
        ]);
    }

    public function postVentas_grid() {       

        
        return $this->reporteRepo->ventasPorMes();
    }

    public function postProductos_grid($y,$m) {       

        
        return $this->reporteRepo->productoPorMes($y,$m);
    }

    public function getProductos() {
        return $this->render('reporte/productos.twig', [
            'title' => 'Reporte'
        ]);
    }

    public function postInventarios_grid($y,$m) { 
        //print_r($this->reporteRepo->inventariosPorMes($y,$m));exit();
        return $this->reporteRepo->inventariosPorMes($y,$m);
    }

    public function getInventarios() {
        return $this->render('reporte/inventarios.twig', [
            'title' => 'Reporte',
            'mainmenu' => 'reportes',
            'submenu' => 'inventarios',
        ]);
    }
}