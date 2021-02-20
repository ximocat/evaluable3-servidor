<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\DirectorModel;

class Director extends ResourceController
{
    protected $modelName = 'App\Models\DirectorModel';
    protected $format = 'json';

    //Método que nos devuelve un array con los dotos y el estado de la peticion
    private function genericResponse($data, $msj, $code)
    {
        if ($code == 200) {
            return $this->respond(array(
                "data" => $data,
                "code" => $code
            )); //, 404, "No hay nada"
        } else {
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
                    array("rel" => "self","href" => $this->url("/director/".$row['id']), "action"=>"PUT", "types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/director/".$row['id']), "action"=>"PATCH" ,"types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/director/".$row['id']), "action"=>"DELETE", "types"=> [] )
                )
               
            );
            array_push($directores, $director);
        }
        return $directores;
    }

    public function index(){
        $data=$this->model->getAll();
        $directores = $this->map($data);

        return $this->genericResponse($directores,null,200);
    }

    public function show($id = null)
    {
        
        $data = $this->model->get($id);      
        $director = $this->map($data); 

        return $this->genericResponse($director, null, 200);
    }


}
