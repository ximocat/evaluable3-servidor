<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PeliculaModel;
use App\Models\PeliculaActor;
use App\Models\PeliculaDirector;
use App\Models\PeliculaActorModel;
use App\Models\PeliculaDirectorModel;

class Restpelicula extends ResourceController
{
    protected $modelPelicula = 'App\Models\Pelicula';
    protected $modelDirector = 'App\Models\Director';
    protected $modelActor = 'App\Models\Actor';
    protected $modelPeliculaActor = 'App\Models\PeliculaActor';
    protected $modelPeliculaDirector = 'App\Models\PeliculaDirector';
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
        $peliculas = array();
        foreach($data as $row){
            $pelicula = array(
                "id" => $row['id'],
                "titulo" => $row['titulo'],
                "anyo" => $row['anyo'],
                "duracion" => $row['duracion'],
                "links" => array(
                    array("rel" => "self","href" => $this->url("/pelicula/".$row['id']),"action" => "GET", "types" =>["text/xml","application/json"]),
                    array("rel" => "self","href" => $this->url("/pelicula/".$row['id']), "action"=>"PUT", "types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/pelicula/".$row['id']), "action"=>"PATCH" ,"types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/pelicula/".$row['id']), "action"=>"DELETE", "types"=> [] )
                )
               
            );
            array_push($peliculas, $pelicula);
        }
        return $peliculas;
    }


}
