<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class PeliculaModel extends Model
{
    protected $table = 'peliculas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['titulo', 'anyo', 'duracion'];
    
    //Metodo que devuelve una única peli cuyo id se pasa por parámetro
    public function get($id){  
        
        //SELECT p.*, d.id as id_d, d.nombre as nombre_d, d.anyoNacimiento as anyoNacimiento_d, d.pais as pais_d FROM peliculas p, directores d, peliculas_directores x where (p.id=x.id_pelicula && d.id=x.id_director)
        //SELECT a.* FROM actores a, peliculas_actores x where x.id_pelicula = 1 and x.id_actor=a.id
        $sql = "SELECT * FROM peliculas WHERE peliculas.id=:id:";
        $query = $this->query($sql, [
                'id'     => $id,               
        ]);    
        return $query->getResult('array');
    }

    public function getAll(){
        $sql = "SELECT * FROM peliculas";
    }
}