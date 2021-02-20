<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class PeliculaActorModel extends Model
{
    protected $table = 'peliculas_actores';
    protected $primaryKey = 'id_pelicula';
    protected $allowedFields = ['id_actor'];

    public function get($id){     
        $sql = "SELECT id_actor FROM peliculas_actores WHERE id_pelicula=:id:";
        $query = $this->query($sql, [
                'id'     => $id,               
        ]);    
        return $query->getResult('array');
    }
    
}