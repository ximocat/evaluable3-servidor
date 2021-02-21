<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class DirectorModel extends Model
{
    protected $table = 'directores';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre', 'anyoNacimiento', 'pais'];
    
    //Metodo que devuelve un único director cuyo id se pasa por parámetro
    public function get($id){     
        $sql = "SELECT * FROM directores WHERE directores.id=:id:";
        $query = $this->query($sql, [
                'id'     => $id,               
        ]);    
        return $query->getResult('array');
    }

    //Metodo que devuelve todos los directores
    public function getAll(){
        $query = $this->query("SELECT * FROM directores");
        return $query->getResult('array');
    }
}