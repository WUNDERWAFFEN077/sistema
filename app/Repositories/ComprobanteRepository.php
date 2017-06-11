<?php
namespace App\Repositories;

use Core\{Auth, Log};
use App\Helpers\{ResponseHelper,AnexGridHelper};
use App\Models\{Comprobante, Kardex, Stock};
use App\Repositories\ProductoRepository as Producto;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
//Buscar como usar BD. Un paquete para realizar transacciones

class ComprobanteRepository {
    private $comprobante;

    public function __construct(){
        $this->comprobante = new Comprobante;
        //$this->comprobante->fecha =date("Y-m-d");
    }

    public function obtener($id) : Comprobante {
        $model = new Comprobante();

        try {
            $model = $this->comprobante->find($id);
        } catch (Exception $e) {
            Log::error(ComprobanteRepository::class, $e->getMessage());
        }

        return $model;
    }

    public function listar(string $q, string $docs) : string {
        $anexgrid = new AnexGridHelper;
        $q = rawurldecode($q);

        $c_docs="";
        if ($docs!='') {
            $c_docs = " AND t1.tipo IN ($docs) ";
        }

        try {
            $sql = "SELECT t1.id,t1.tipo,t1.serie,t1.numero,nombre,fecha,sub_total,iva,total
                    FROM comprobante t1
                    INNER JOIN cliente t2 ON (t1.cliente_id=t2.id)
                    WHERE (nombre LIKE '%$q%' OR t1.id LIKE '%$q%') 
                    $c_docs
                    ORDER BY $anexgrid->columna $anexgrid->columna_orden
                    LIMIT  $anexgrid->pagina, $anexgrid->limite
            ";
            
            $result = DB::select($sql);

            $sql = "SELECT COUNT(*) as t 
                    FROM comprobante t1
                    INNER JOIN cliente t2 ON (t1.cliente_id=t2.id)
                    WHERE (nombre LIKE '%$q%' OR t1.id LIKE '%$q%') 
                    $c_docs
            ";            
            $total = DB::select($sql);

            

            return $anexgrid->responde(
                $result,
                $total[0]->t
            );
            
        } catch (Exception $e) {
            Log::error(ComprobanteRepository::class, $e->getMessage());
        }

        return "";
    }

    public function generar(Comprobante $model, array $detalle) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            DB::BeginTransaction();
            $this->comprobante->id = $model->id;
            $this->comprobante->cliente_id = $model->cliente_id;
            $this->comprobante->fecha = $model->fecha;
            $this->comprobante->tipo = $model->tipo;
            $this->comprobante->serie = $model->serie;
            $this->comprobante->numero = $model->numero;
            $this->comprobante->sub_total = 0;
            $this->comprobante->iva = 0;
            $this->comprobante->total = 0;
            $this->comprobante->estado = 1;


            foreach ($detalle as $k => $d) {
                //$d->exists = false;
                if(!empty($d->id)){               
                    $d->exists = true;
                }
                $d->total = $d->cantidad * $d->precio;
                $this->comprobante->total += $d->total;
               
            }
            
            $this->comprobante->sub_total = $this->comprobante->total / 1.18;
            $this->comprobante->iva = $this->comprobante->total - $this->comprobante->sub_total;

            //$this->comprobante = $model;
            if(!empty($model->id)){                
                $this->comprobante->exists = true;
            }
            //Generar Comprobante
            //$model->save();
            $this->comprobante->save();
            //Guardar Detalle
            //$model->detalle()->saveMany($detalle);           
            $this->comprobante->detalle()->saveMany($detalle);

            $rh->setResponse(true);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(ProductoRepository::class, $e->getMessage());
        }

        return $rh;
    }

    
    /*
    public function listar() : string {
        $anexgrid = new AnexGridHelper;

        try {
            $result = $this->comprobante->orderBy(
                $anexgrid->columna,
                $anexgrid->columna_orden
            )->skip($anexgrid->pagina)
             ->take($anexgrid->limite)
             ->get();

            foreach($result as $r) {
                $r->cliente = $r->cliente;
            }

            return $anexgrid->responde(
                $result,
                $this->comprobante->count()
            );
        } catch (Exception $e) {
            Log::error(ComprobanteRepository::class, $e->getMessage());
        }

        return "";
    }
    */

    /*
    Se procesa el comprobante, se registra el kardex y se actualiza el stock.
    */
    public function procesar(int $id) : ResponseHelper {
        $rh = new ResponseHelper;
        //$productoRepo = new ProductoRepository();

        $kardex  = new Kardex;
        try {
            DB::BeginTransaction();
            $this->comprobante->id = $id;
            $this->comprobante->estado = 2;
            $this->comprobante->exists = true;

            //Detalle
            $detalles = $this->comprobante->detalle()->get();

            $c="";
            foreach ($detalles as $detalle) {
                $producto_id = $detalle->producto_id;
                $cantidad = $detalle->cantidad;
                $stock = Producto::checkStock($producto_id);
                //$c .= $stock.",";

                //REGISTRAR KARDEX
                $kardex->producto_id = $producto_id;
                $kardex->tipo = 200;
                $kardex->documento_id  = $id;
                $kardex->cantidad = $cantidad;
                $kardex->save();


                //REGISTRAR STOCK
                $productoStock = Stock::where('producto_id', $producto_id)->first();
                //print_r($articleStock);exit();
                if(!empty($productoStock))
                {
                    $productoStock->cantidad -= $cantidad;
                    $productoStock->save();
                }/*else{                 
                    $stock = new Stock; 
                    $stock->producto_id = $producto_id;
                    $stock->cantidad = $cantidad;
                    $stock->save();
                }*/ #if !empty($ArticleStock)
                
            }

            //ELIMINAR STOCK, CANTIDAD CERO
            Stock::where('cantidad', 0)->delete();           
            

            //print_r($c);exit();

            $this->comprobante->save();

            DB::commit();
            $rh->setResponse(true);
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(ComprobanteRepository::class, $e->getMessage());
        }

        return $rh;
    }

    public function anular(int $id) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            $this->comprobante->id = $id;
            $this->comprobante->estado = 1;
            $this->comprobante->exists = true;

            $this->comprobante->save();
            $rh->setResponse(true);
        } catch (Exception $e) {
            Log::error(ComprobanteRepository::class, $e->getMessage());
        }

        return $rh;
    }

    
}