<?php
namespace App\Repositories;

use Core\{Auth, Log};
use App\Helpers\{ResponseHelper,AnexGridHelper};
use App\Models\{Producto,  Stock};
use Exception;
use Intervention\Image\ImageManagerStatic As Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Capsule\Manager as DB;

class ProductoRepository {
    private $producto;   

    public function __construct(){
        $this->producto = new Producto;

    }

    public function listar(string $q) : string {
        $anexgrid = new AnexGridHelper;
        $q = strtolower($q);

        try {
            $sql = "SELECT id, nombre, marca, costo, precio, foto
                    FROM producto
                    WHERE LOWER(nombre) LIKE '%$q%'
                    ORDER BY $anexgrid->columna $anexgrid->columna_orden
                    LIMIT  $anexgrid->pagina, $anexgrid->limite
            ";
            $result = DB::select($sql);

            $sql = "SELECT COUNT(*) as t 
                    FROM producto
                    WHERE LOWER(nombre) LIKE '%$q%'
            ";
            $total = DB::select($sql);

            

            return $anexgrid->responde(
                $result,
                $total[0]->t
            );
            
        } catch (Exception $e) {
            Log::error(ProductoRepository::class, $e->getMessage());
        }

        return "";
    }

    /*public function listar() : string {
        $anexgrid = new AnexGridHelper;

        try {
            $result = $this->producto->orderBy(
                $anexgrid->columna,
                $anexgrid->columna_orden
            )->skip($anexgrid->pagina)
             ->take($anexgrid->limite)
             ->get();

            return $anexgrid->responde(
                $result,
                $this->producto->count()
            );
        } catch (Exception $e) {
            Log::error(ProductoRepository::class, $e->getMessage());
        }

        return "";
    }
    */

    


    public function guardar(Producto $model, array $foto = null) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            $this->producto->id = $model->id;
            $this->producto->nombre = $model->nombre;
            $this->producto->marca = $model->marca;
            $this->producto->precio = $model->precio;
            $this->producto->costo = $model->costo;

            if(!empty($model->id)){
                
                $this->producto->exists = true;
            }

            if (!is_null($foto)) {
                $nombre_archivo = sprintf(
                    'media/producto-%s.%s',
                    $model->id,
                    pathinfo($foto['name'], PATHINFO_EXTENSION)
                );

                $img = Image::make($foto['tmp_name']);
                // resize image instance
                $img->resize(500, 500);
                // save image in desired format
                $img->save('public/'.$nombre_archivo);

                $this->producto->foto = $nombre_archivo;
            }
            

            $this->producto->save();
            $rh->setResponse(true);
        } catch (Exception $e) {
            Log::error(ProductoRepository::class, $e->getMessage());
        }

        return $rh;
    }

    public function obtener($id) : Producto {
        $producto = new Producto;

        try {
            $producto = $this->producto->find($id);
        } catch (Exception $e) {
            Log::error(ProductoRepository::class, $e->getMessage());
        }

        return $producto;
    }    

    public function eliminar(int $id) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            $this->producto->destroy($id);
            $rh->setResponse(true);
        } catch (Exception $e) {
            Log::error(ProductoRepository::class, $e->getMessage());
        }

        return $rh;
    }


    public function importar(array $archivo) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            $data = [];
            $fila = 0;
            if (($gestor = fopen($archivo['tmp_name'], "r")) !== FALSE) {
                while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {
                    if ($fila>0) {
                        $model = new Producto;
                        $model->nombre = $datos[0];
                        $model->marca = $datos[1];
                        $model->costo = $datos[2];
                        $model->precio = $datos[3];
                        $data[] = $model;
                    }                    
                    $fila++;
                }
                fclose($gestor);
            }
            
            foreach ($data as $d){
               $d->save();
            }
            
            $rh->setResponse(true);
        } catch (Exception $e) {
            Log::error(ProductoRepository::class, $e->getMessage());
        }

        return $rh;
    }


    public function todo() : Collection {
        $result = null;

        try {
            $result = $this->producto->orderBy('id','DESC')
             ->get();

        } catch (Exception $e) {
            Log::error(ProductoRepository::class, $e->getMessage());
        }

        return $result;
    }

    public function buscar(string $q) : array {
        $result = [];
        try {
            $result = $this->producto
                            ->where('nombre','like',"%$q%")
                            ->orderBy('nombre')
                            ->get()
                            ->toArray();
            
        } catch (Exception $e) {
            Log::error(ProductoRepository::class, $e->getMessage());
        }

        return $result;

    }

    

    public function checkExtension($extension):bool
    {
       
        //aqui podemos añadir las extensiones que deseemos permitir
        $extensiones = array("gif","jpg","jpeg","JPG","JPEG","png");
        if(in_array(strtolower($extension), $extensiones))
        {
            return TRUE;
        }else{
            return FALSE;
        }
        
    }


    public function cargarImagen(Producto $model, array $file = null): array{
        
        
        try {
            $matriz=array();    
           

            $this->guardar($model,$file);

            $producto = $this->obtener($model->id);
            

            /*if(checkExtension($extension) === TRUE)
            {
                guardar($model,$file);
                $matriz['estado']='SI';
            }else{
                $matriz['estado']='NO';
            }*/
            $matriz['imagen']="public/".$producto->foto.'';
            
            return $matriz;
            
        } catch (Exception $e) {
            Log::error(ProductoRepository::class, $e->getMessage());
        }
        
    }

    /*STOCK*/
    public static function checkStock($id)
    {
        
        $articleStock = Stock::whereRaw("producto_id='". $id ."'")->first();
        if (empty($articleStock)) {
            //return 'NO';
            return false;
        } else {
            //return 'SI';
            return true;
        }
        
    }


    
    
}