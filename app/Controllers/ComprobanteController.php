<?php
namespace App\Controllers;

use App\Repositories\ComprobanteRepository;
use Core\{Controller};
use App\Models\Comprobante;
use App\Models\ComprobanteDetalle;
use Dompdf\Dompdf;
use Dompdf\Exception;


class ComprobanteController extends Controller {
    private $comprobanteRepo;

    public function __construct() {
        parent::__construct();
        $this->comprobanteRepo = new ComprobanteRepository();
    }

    public function getIndex() {
        return $this->render('comprobante/index.twig', [
            'title' => 'Comprobantes',
            'mainmenu' => 'ventas',
            'submenu' => 'factura'
        ]);
    }

    public function postGrid() {
        $param = $_POST['parametros'];
        $q=$param[0]['q'];
        $docs=$param[0]['docs'];
        
        return $this->comprobanteRepo->listar($q,$docs);
    }

    public function getCrud($id=0) {
        /*$model = (
            $id === 0
                ? new Comprobante;                
                : $this->productoRepo->obtener($id)
        );
        */

        if ($id===0) {
            $model = new Comprobante; 
            $model->fecha = date("Y-m-d");
            $model->sub_total = 0;
        }else{
            $model = $this->comprobanteRepo->obtener($id);
        }
        //$model = new Comprobante(); 
        //$model->fecha = date("Y-m-d");
        return $this->render('comprobante/crud.twig', [
            'title' => 'Comprobantes',
            'mainmenu' => 'ventas',
            'submenu' => 'factura',
            'model' => $model
        ]);
    }

    /*
    public function getDetalle($id) {
        return $this->render('comprobante/detalle.twig', [
            'title' => 'Comprobantes',
            'model' => $this->comprobanteRepo->obtener($id)
        ]);
    }*/

    public function postGenerar() {   
        //var_dump($_POST);
        $model = new Comprobante();  
        $model->id = $_POST['id'];
        $model->cliente_id = $_POST['cliente_id']; 
        $model->fecha = $_POST['fecha'];
        $model->tipo = $_POST['tipo'];
        $model->serie = $_POST['serie'];
        $model->numero = $_POST['numero'];


        $detalle = [];
        
        foreach ($_POST['detalle'] as $d) {
            $d = (object)$d;
            $cd = new ComprobanteDetalle();
            $cd->id = $d->id;
            $cd->producto_id = $d->producto_id;
            $cd->cantidad = $d->cantidad;
            $cd->costo = $d->costo;
            $cd->precio = $d->precio;

            $detalle[] = $cd;
        }
        /*
        print_r(
            json_encode(
                $detalle
            )
        );
        */
        

        print_r(
            json_encode(
                $this->comprobanteRepo->generar($model,$detalle)
            )
        );
        
    }


    public function getPdf($id) {
        $model = $this->comprobanteRepo->obtener($id);

        if ($model->anulado == 1) {
            throw new Exception("Comprobante Anulado");
        }

        $dompdf = new Dompdf();
        $dompdf->loadHtml(
            $this->render('comprobante/pdf.twig', [
                'model' => $model
            ])
        );

        $dompdf->setPaper('A4', 'landscape');

        $dompdf->render();
        $dompdf->stream('comprobante-'.$model->idForView);
    }

    public function postProcesar(){
        print_r(
            json_encode(
                $this->comprobanteRepo->procesar($_POST['id'])
            )
        );
    }


    public function postAnular(){
        print_r(
            json_encode(
                $this->comprobanteRepo->anular($_POST['id'])
            )
        );
    }


}