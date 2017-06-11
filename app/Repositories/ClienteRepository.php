<?php
namespace App\Repositories;

use Core\{Auth, Log};
use App\Helpers\{ResponseHelper,AnexGridHelper};
use App\Models\{Cliente};
use Exception;
use Illuminate\Database\Capsule\Manager as DB;

class ClienteRepository {
    private $cliente;

    public function __construct(){
        $this->cliente = new Cliente;
    }

    public function listar(string $q) : string {
        $anexgrid = new AnexGridHelper;
        $q = rawurldecode($q);

        try {
            $sql = "SELECT id, nombre, telefono, email, direccion
                    FROM cliente
                    WHERE nombre LIKE '%$q%' 
                    ORDER BY $anexgrid->columna $anexgrid->columna_orden
                    LIMIT  $anexgrid->pagina, $anexgrid->limite
            ";
            $result = DB::select($sql);

            $sql = "SELECT COUNT(*) as t 
                    FROM cliente
                    WHERE nombre LIKE '%$q%' 
            ";
            $total = DB::select($sql);

            

            return $anexgrid->responde(
                $result,
                $total[0]->t
            );
            
        } catch (Exception $e) {
            Log::error(ClienteRepository::class, $e->getMessage());
        }

        return "";
    }

    /*
    public function listar() : string {
        $anexgrid = new AnexGridHelper;

        try {
            $result = $this->cliente->orderBy(
                $anexgrid->columna,
                $anexgrid->columna_orden
            )->skip($anexgrid->pagina)
             ->take($anexgrid->limite)
             ->get();

            return $anexgrid->responde(
                $result,
                $this->cliente->count()
            );
        } catch (Exception $e) {
            Log::error(ClienteRepository::class, $e->getMessage());
        }

        return "";
    }
    */


    public function guardar(Cliente $model) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            $this->cliente->id = $model->id;
            $this->cliente->nombre = $model->nombre;
            $this->cliente->telefono = $model->telefono;
            $this->cliente->email = $model->email;
            $this->cliente->direccion = $model->direccion;

            if(!empty($model->id)){
                
                $this->cliente->exists = true;
            }

            $this->cliente->save();
            $rh->setResponse(true);
        } catch (Exception $e) {
            Log::error(ClienteRepository::class, $e->getMessage());
        }

        return $rh;
    }

    public function obtener($id) : Cliente {
        $cliente = new Cliente;

        try {
            $cliente = $this->cliente->find($id);
        } catch (Exception $e) {
            Log::error(ClienteRepository::class, $e->getMessage());
        }

        return $cliente;
    }    

    public function eliminar(int $id) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            $this->cliente->destroy($id);
            $rh->setResponse(true);
        } catch (Exception $e) {
            Log::error(ClienteRepository::class, $e->getMessage());
        }

        return $rh;
    }


    public function buscar(string $q) : array {
        $q=rawurldecode($q);
        $result = [];
        try {
            $result = $this->cliente
                            //->where('nombre','like',"%".strtolower($q)."%")
                            ->where('nombre','like',"%$q%")
                            ->orderBy('nombre')
                            ->get()
                            ->toArray();
            
        } catch (Exception $e) {
            Log::error(ClienteRepository::class, $e->getMessage());
        }

        return $result;

    }
    
}