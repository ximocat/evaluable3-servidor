<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class ActorModel extends Model
{
    protected $table = 'actores';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre', 'anyoNacimiento', 'pais'];
    
    //Metodo que devuelve un único actor cuyo id se pasa por parámetro
    public function get($id){     
        $sql = "SELECT * FROM actores WHERE actores.id=:id:";
        $query = $this->query($sql, [
                'id'     => $id,               
        ]);    
        return $query->getResult('array');
    }

    //Metodo que devuelve todos los actores
    public function getAll(){
        $query = $this->query("SELECT * FROM actores");
        return $query->getResult('array');
    }
}