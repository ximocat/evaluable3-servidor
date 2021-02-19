<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class PeliculaModel extends Model
{
    protected $table = 'peliculas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['titulo', 'anyo', 'duracion'];
    
    /**
     * Obtenemos un único equipo
     */
    public function get($id){     
        $sql = "SELECT * FROM `equipos` WHERE equipos.id=:id:";
        $query = $this->query($sql, [
                'id'     => $id,               
        ]);    
        return $query->getResult('array');
     }
}