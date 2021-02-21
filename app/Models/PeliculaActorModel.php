<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class PeliculaActorModel extends Model
{
    protected $table = 'peliculas_actores';
    protected $allowedFields = ['id_pelicula','id_actor'];

    //Metodo que devuelve los actores que han hecho la peli cuyo id se pasa
    //por parametro
    public function get($id){     
        $sql = "SELECT id_actor FROM peliculas_actores WHERE id_pelicula=:id:";
        $query = $this->query($sql, [
                'id'     => $id,               
        ]);    
        return $query->getResult('array');
    }
    
}