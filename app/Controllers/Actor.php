<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PeliculaModel;
use App\Models\ActorModel;
use App\Models\DirectorModel;

class Actor extends ResourceController
{
    protected $modelName = 'App\Models\VideoclubModel';
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
        $actores = array();
        foreach($data as $row){
            $actor = array(
                "id" => $row['id'],
                "titulo" => $row['titulo'],
                "anyo" => $row['anyo'],
                "duracion" => $row['duracion'],
                "links" => array(
                    array("rel" => "self","href" => $this->url("/actor/".$row['id']),"action" => "GET", "types" =>["text/xml","application/json"]),
                    array("rel" => "self","href" => $this->url("/actor/".$row['id']), "action"=>"PUT", "types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/actor/".$row['id']), "action"=>"PATCH" ,"types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/actor/".$row['id']), "action"=>"DELETE", "types"=> [] )
                )
               
            );
            array_push($actores, $actor);
        }
        return $actores;
    }


}
