<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class PeliculaModel extends Model
{
    protected $table = 'peliculas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['titulo', 'anyo', 'duracion'];
    
    //Metodo que devuelve una única peli cuyo id se pasa por parámetro
    public function get($id){     
        $sql = "SELECT * FROM 'peliculas' WHERE peliculas.id=:id:";
        $query = $this->query($sql, [
                'id'     => $id,               
        ]);    
        return $query->getResult('array');
     }
}