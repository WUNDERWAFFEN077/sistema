<?php
namespace App\Helpers;

class DataTableHelper
{
    public $limite = 0;
    public $pagina = 0;
    public $columna = '';
    public $columna_orden = '';
    public $filtros = [];
    public $parametros = [];
    
    public function __CONSTRUCT()
    {
        /* Cantidad de registros por página */
        $this->limite = $_REQUEST['length'];
        if(!is_numeric($this->limite)) return;
        
        /* Desde que número de fila va a paginar */
        $this->pagina = $_REQUEST['start'] - 1;
        if(!is_numeric($this->pagina)) return;
        
        if( $this->pagina > 0) $this->pagina = $this->pagina * $this->limite;
        
        /* Ordenamiento de las filas */
        $this->columna = $_REQUEST['order']['0']['column'];
        $this->columna_orden = $_REQUEST['order']['0']['dir'];
        
        /* Filtros */
        if(isset($_REQUEST['filtros']))
            $this->filtros = $_REQUEST['filtros'];
        
        /* Parametros adicionales */
        if(isset($_REQUEST['parametros']))
            $this->parametros = $_REQUEST['parametros'];
    }
    
    public function responde($data, $total)
    {
        return json_encode(array(
            'data' => $data,
            'total' => $total
        ));
    }
}