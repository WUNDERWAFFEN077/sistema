<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model{
    protected $table = 'kardex';

    public function producto()
    {
        return $this->belongsTo('App\Models\Producto');
    }
}