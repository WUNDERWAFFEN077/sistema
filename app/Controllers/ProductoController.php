<?php
namespace App\Controllers;
use App\Helpers\{ResponseHelper};
use App\Validations\ProductoValidation;
use App\Repositories\{ProductoRepository};
use Core\{Controller};
use App\Models\Producto;

class ProductoController extends Controller {
    private $productoRepo;
    public function __construct() {
        parent::__construct();
        $this->productoRepo = new ProductoRepository();
    }

    public function getIndex() {
        return $this->render('producto/index.twig', [
            'title' => 'Productos',
            'mainmenu' => 'producto',
        ]);
    }

    public function postGrid() {
        $param = $_POST['parametros'];
        $q=$param[0]['q'];
        return $this->productoRepo->listar($q);
    }
  

    public function getCrud($id=0) {
        $model = (
            $id === 0
                ? new Producto
                : $this->productoRepo->obtener($id)
        );
        return $this->render('producto/crud.twig', [
            'title' => 'Productos',
            'model' => $model,
            'mainmenu' => 'producto',
            'stock' => $this->productoRepo::checkStock($id)
        ]);
    }

    public function postGuardar() {
        ProductoValidation::validate($_POST);

        $model = new Producto;
        $model->id = $_POST['id'];
        $model->nombre = $_POST['nombre'];
        $model->marca = $_POST['marca'];
        $model->precio = $_POST['precio'];
        $model->costo = $_POST['costo'];

        $foto = null;

        if (!empty($_FILES['foto'])) {
            $foto = $_FILES['foto'];
        }
        $rh = $this->productoRepo->guardar($model,$foto);

        if($rh->response) {
            $rh->href = 'producto';
        }
        
        print_r(
            json_encode($rh)
        );
    }

    public function postEliminar() {
        print_r(
            json_encode(
                $this->productoRepo->eliminar($_POST['id'])
            )
        );
    }

    public function postImportar(){
        $rh = new ResponseHelper();

        if (empty($_FILES['archivo'])) {
            $rh->setResponse(false, 'Debe Abjuntar un archivo.');
            
        }else{
            $rh = $this->productoRepo->importar($_FILES['archivo']);

            if ($rh->response) {
                $rh->href = 'self';
            }
        }

        print_r(
                json_encode(
                    $rh
                )
            );

    }

    public function getExportar() {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=mi_archivo.xls"); 
        header("Pragma: no-cache");
        header("Expires: 0");
        
        return $this->render('producto/excel.twig', [           
            'model' => $this->productoRepo->todo()
        ]);
    }

    public function getBuscar($q){
        print_r(
            json_encode(
                $this->productoRepo->buscar($q)
            )
        );
    }

    public function postCargar(){
        $model = new Producto;
        $model->id = $_POST['id'];
        $model->nombre = $_POST['nombre'];
        $model->marca = $_POST['marca'];
        $model->precio = $_POST['precio'];
        $model->costo = $_POST['costo'];

        $foto = null;

        if (!empty($_FILES['foto'])) {
            $foto = $_FILES['foto'];
        }
        
        print_r(
            json_encode(
                $this->productoRepo->cargarImagen($model,$foto)
            )
        );
    }

}