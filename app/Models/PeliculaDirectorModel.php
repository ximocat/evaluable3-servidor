<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class PeliculaDirectorModel extends Model
{
    protected $table = 'peliculas_directores';
    protected $allowedFields = ['id_pelicula','id_director'];

    public function get($id){     
        $sql = "SELECT id_director FROM peliculas_directores WHERE id_pelicula=:id:";
        $query = $this->query($sql, [
                'id'     => $id,               
        ]);    
        return $query->getResult('array');
    }
    
}