<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\DirectorModel;

class Director extends ResourceController
{
    protected $modelName = 'App\Models\DirectorModel';
    protected $format = 'json';

    //Método que nos devuelve un array con los datos y el estado de la peticion
    private function genericResponse($data, $msj, $code)
    {
        if ($code == 200) { //todo OK
            return $this->respond(array(
                "data" => $data,
                "code" => $code
            )); 
        } else { //Error
            return $this->respond(array(
                "msj" => $msj,
                "code" => $code
            ));
        }
    }

    //Método que nos devuelve la URL
    private function url($segmento){
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        }
        else{
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $segmento;
    }

    //Funcion a la que le pasamos los datos y los prepara para ser enviados
    private function map($data){
        $directores = array();
        foreach($data as $row){
            $director = array(
                "id" => $row['id'],
                "nombre" => $row['nombre'],
                "anyoNacimiento" => $row['anyoNacimiento'],
                "pais" => $row['pais'],
                "links" => array(
                    array("rel" => "self","href" => $this->url("/director/".$row['id']),"action" => "GET", "types" =>["text/xml","application/json"]),
                    array("rel" => "self","href" => $this->url("/director/".$row['id']), "action"=>"POST", "types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/director/".$row['id']), "action"=>"PUT" ,"types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/director/".$row['id']), "action"=>"DELETE", "types"=> [] )
                )
               
            );
            array_push($directores, $director);
        }
        return $directores;
    }

    //Metodo utilizado para las operaciones GET de todos los datos
    public function index(){
        $data=$this->model->getAll();
        $directores = $this->map($data);

        return $this->genericResponse($directores,null,200);
    }

    //Metodo utilizado para las operaciones GET de un solo dato cuya id pasamos
    //por parametro
    public function show($id = null)
    {
        
        $data = $this->model->get($id);      
        $director = $this->map($data); 

        return $this->genericResponse($director, null, 200);
    }

    //Metodo utilizado para las operaciones POST
    public function create()
    {
 
        $director = new DirectorModel();
 
        if ($this->validate('director')) {
 
            $id = $director->insert([
                'nombre' => $this->request->getPost('nombre'),
                'anyoNacimiento' => $this->request->getPost('anyoNacimiento'),               
                'pais' => $this->request->getPost('pais')
            ]);
 

            return $this->genericResponse($this->model->get($id), null, 200);
        }
 
        //Validacion de director
        $validation = \Config\Services::validation();
        //Devolvemos error si no es validado
        return $this->genericResponse(null, $validation->getErrors(), 500);
    }

    //Metodo utilizado para las operaciones PUT/PATCH
    public function update($id = null)
    {
        $director = new DirectorModel();

        $data = $this->request->getRawInput();
 
        if ($this->validate('director')) {
 
            if (!$director->get($id)) {
                return $this->genericResponse(null, array("id" => "El director no existe"), 500);
            }
 
            $director->update($id, [
                'nombre' => $data['nombre'],
                'anyoNacimiento' => $data['anyoNacimiento'],             
                'pais' => $data['pais']
            ]);
 
            return $this->genericResponse($this->model->get($id), null, 200);
        }

    }

    //Metodo utilizado para las operaciones DELETE
    public function delete($id = null)
    {
        $director = new DirectorModel();
        //Comprobamos antes de borrar si existe
        if(!$this->model->get($id)){
            return $this->genericResponse(null, array("id" => "El director no existe"), 500);    
        }else{
            $director->delete($id);
            return $this->genericResponse("Director eliminado", null, 200);
        }
    }


}
