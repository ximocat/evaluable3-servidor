<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ActorModel;

class Actor extends ResourceController
{
    protected $modelName = 'App\Models\ActorModel';
    protected $format = 'json';

    //Método que nos devuelve un array con los datos y el estado de la peticion
    private function genericResponse($data, $msj, $code)
    {
        if ($code == 200) {//todo correcto
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
        $actores = array();
        foreach($data as $row){
            $actor = array(
                "id" => $row['id'],
                "nombre" => $row['nombre'],
                "anyoNacimiento" => $row['anyoNacimiento'],
                "pais" => $row['pais'],
                "links" => array(
                    array("rel" => "self","href" => $this->url("/actor/".$row['id']),"action" => "GET", "types" =>["text/xml","application/json"]),
                    array("rel" => "self","href" => $this->url("/actor/".$row['id']), "action"=>"POST", "types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/actor/".$row['id']), "action"=>"PUT" ,"types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/actor/".$row['id']), "action"=>"DELETE", "types"=> [] )
                )
               
            );
            array_push($actores, $actor);
        }
        return $actores;
    }

    //Metodo utilizado para las operaciones GET de todos los datos
    public function index(){
        $data=$this->model->getAll();
        $actores = $this->map($data);

        return $this->genericResponse($actores,null,200);
    }

    //Metodo utilizado para las operaciones GET de un solo dato cuya id pasamos
    //por parametro
    public function show($id = null)
    {
        
        $data = $this->model->get($id);      
        $actor = $this->map($data); 

        return $this->genericResponse($actor, null, 200);
    }

    //Metodo utilizado para las operaciones POST
    public function create()
    {
         $actor = new ActorModel();
 
        if ($this->validate('actor')) {
 
            $id = $actor->insert([
                'nombre' => $this->request->getPost('nombre'),
                'anyoNacimiento' => $this->request->getPost('anyoNacimiento'),               
                'pais' => $this->request->getPost('pais')
            ]);
 

            return $this->genericResponse($this->model->get($id), null, 200);
        }
 
        //Validacion de actor
        $validation = \Config\Services::validation();
        //Devolvemos error si no es validado
        return $this->genericResponse(null, $validation->getErrors(), 500);
    }


    //Metodo utilizado para las operaciones PUT/PATCH
    public function update($id = null)
    {
        $actor = new ActorModel();

        $data = $this->request->getRawInput();
 
        if ($this->validate('actor')) {
 
            if (!$actor->get($id)) {
                return $this->genericResponse(null, array("id" => "El actor no existe"), 500);
            }
 
            $actor->update($id, [
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
 
        $actor = new ActorModel();
        //Comprobamos antes de borrar si existe
        if(!$this->model->get($id)){
            return $this->genericResponse(null, array("id" => "El actor no existe"), 500);    
        }else{
            $actor->delete($id);
            return $this->genericResponse("Actor eliminado", null, 200);
        }
    }

}
