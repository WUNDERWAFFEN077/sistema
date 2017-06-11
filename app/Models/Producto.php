<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model{
    protected $table = 'producto';    

    //Mutator de Eloquent permite convertir una funcion en un atributo, usando un formato
    public function getMargenAttribute() : float{
    	$ingreso = $this->precio - $this->costo;
    	return round($ingreso / $this->costo * 100,0);
    }

    public function getTieneFotoAttribute() : string{
    	return empty($this->foto) ? 'No':'Si';
    }

    public function stocks()
    {
        return $this->hasMany('App\Models\Stock');
    }
}