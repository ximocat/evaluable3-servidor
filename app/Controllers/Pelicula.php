<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PeliculaModel;
use App\Models\ActorModel;
use App\Models\DirectorModel;
use App\Models\PeliculaActorModel;
use App\Models\PeliculaDirectorModel;

class Pelicula extends ResourceController
{
    protected $modelName = 'App\Models\PeliculaModel';
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
    private function url($segmento)
    {
        if (isset($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        } else {
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $segmento;
    }

    //Funcion a la que le pasamos los datos y los prepara para ser enviados
    private function map($data)
    {
        $peliculas = array();
        foreach ($data as $row) {
            $pelicula = array(
                "id" => $row['id'],
                "titulo" => $row['titulo'],
                "anyo" => $row['anyo'],
                "duracion" => $row['duracion'],
                "actores" => $this->getActores($row['id']),
                "directores" => $this->getDirectores($row['id']),
                "links" => array(
                    array("rel" => "self","href" => $this->url("/pelicula/".$row['id']),"action" => "GET", "types" =>["text/xml","application/json"]),
                    array("rel" => "self","href" => $this->url("/pelicula/".$row['id']), "action"=>"POST", "types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/pelicula/".$row['id']), "action"=>"PUT" ,"types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/pelicula/".$row['id']), "action"=>"DELETE", "types"=> [] )
                )
               
            );
            array_push($peliculas, $pelicula);
        }
        
        return $peliculas;
    }

    //Metodo utilizado para las operaciones GET de todos los datos
    public function index()
    {
        $data=$this->model->getAll();
        $peliculas = $this->map($data);
        return $this->genericResponse($peliculas, null, 200);
    }

    //Metodo utilizado para las operaciones GET de un solo dato cuya id pasamos
    //por parametro
    public function show($id = null)
    {
        $data = $this->model->get($id);
        $pelicula = $this->map($data);

        return $this->genericResponse($pelicula, null, 200);
    }

    //Metodo a la que le pasamos el id de una peli y nos devuelve un array con
    //sus actores
    private function getActores($id_pelicula)
    {
        $modelPeliculaActor=new PeliculaActorModel;
        $modelActor=new ActorModel;

        $actores= array();
        $dataActores=$modelPeliculaActor->get($id_pelicula);
       
        foreach ($dataActores as $row) {
            $dataActor=$modelActor->get($row['id_actor']);
            $dataActor[0]["links"]=$this->makeLinksActor($row['id_actor']);
            $actores[]= $dataActor;
        }
        return $actores;
    }

    //Metodo encargado de generar los links del actur cuyo id se pasa por parametro
    private function makeLinksActor($id_actor)
    {
        $links = array(
                    array("rel" => "self","href" => $this->url("/actor/".$id_actor),"action" => "GET", "types" =>["text/xml","application/json"]),
                    array("rel" => "self","href" => $this->url("/actor/".$id_actor), "action"=>"POST", "types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/actor/".$id_actor), "action"=>"PUT" ,"types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/actor/".$id_actor), "action"=>"DELETE", "types"=> [] )
        );
        return $links;
    }

    //Metodo a la que le pasamos el id de una peli y nos devuelve un array con
    //sus directores
    private function getDirectores($id_pelicula)
    {
        $modelPeliculaDirector=new PeliculaDirectorModel;
        $modelDirector=new DirectorModel;

        $directores=[];
        $dataDirectores=$modelPeliculaDirector->get($id_pelicula);
       
        foreach ($dataDirectores as $row) {
            $dataDirector=$modelDirector->get($row['id_director']);
            $dataDirector[0]["links"]=$this->makeLinksDirector($row['id_director']);
            array_push($directores, $dataDirector);
        }
        return $directores;
    }

    //Metodo encargado de generar los links del director cuyo id se pasa por parametro
    private function makeLinksDirector($id_director)
    {
        $links = array(
                    array("rel" => "self","href" => $this->url("/director/".$id_director),"action" => "GET", "types" =>["text/xml","application/json"]),
                    array("rel" => "self","href" => $this->url("/director/".$id_director), "action"=>"POST", "types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/director/".$id_director), "action"=>"PUT" ,"types" => ["application/x-www-form-urlencoded"]),
                    array("rel" => "self","href" => $this->url("/director/".$id_director), "action"=>"DELETE", "types"=> [] )
        );
        return $links;
    }

    //Metodo utilizado para las operaciones POST
    public function create()
    {
        $pelicula = new PeliculaModel();
        $director =new DirectorModel();
        $actor = new ActorModel();
        $peliculaActor=new PeliculaActorModel();
        $peliculaDirector=new PeliculaDirectorModel();
        $actores=[];
        $directores=[];
        
        
        //Comprobamos si no se ha pasado el director
        if (!$this->request->getPost("id_director")) {
            return $this->genericResponse(null, array("id_director" =>
                "No se ha pasado el id del director por parámetro"), 500);
        }
        //Comprobamos si no existe el director
        if (!$director->get($this->request->getPost("id_director"))) {
            return $this->genericResponse(null, array("id_director" =>
                "El director no existe"), 500);
        }
        //Guardamos los id de los directores en un array. De momento solo habrá
        //un director aunque se deja así como posible mejora a posteriore en la
        //que pueda haber más de un director
        array_push($directores, $director->get($this->request->getPost("id_director"))[0]["id"]);

        //Comprobamos si no se ha pasado el numero de actores
        if (!$this->request->getPost("numActores")) {
            return $this->genericResponse(null, array("numActores" =>
                "No se ha pasado el número de actores por parámetro"), 500);
        }

        //Entero que contendrá el número de actores
        $numActores=(int)$this->request->getPost("numActores");

        //Bucle para comprobar si no se han pasado los id de los actores o que no existan
        for ($i=0;$i<$numActores;$i++) {
            //Comprobamos si no se ha pasado el actores
            if (!$this->request->getPost("id_actor[".$i."]")) {
                return $this->genericResponse(null, array("id_actor[".$i."]" =>
                    "No se ha pasado el id del actor por parámetro"), 500);
            }
            //Comprobamos si algún actor no existe
            if (!$actor->get($this->request->getPost("id_actor[".$i."]"))) {
                return $this->genericResponse(null, array("id_actor[".$i."]" =>
                    "El actor no existe"), 500);
            }
            //Guardamos los id de los actores en un array
            array_push($actores, $actor->get($this->request->getPost("id_actor[".$i."]"))[0]["id"]);
        }

        //Legados a este punto sabemos que se han pasado bien todos los datos
        //de actores y director. Vamos a crear la pelicula y a modificar las
        //tablas PeliculasDirector y PeliculasActor para que reflejen los cambios
        if ($this->validate('pelicula')) {//Comprobamos que pase la validacion
            $id = $pelicula->insert([
                    'titulo' => $this->request->getPost('titulo'),
                    'anyo' => $this->request->getPost('anyo'),
                    'duracion' => $this->request->getPost('duracion')
                ]);
                
                //Introducimos los directores en PeliculasDirector. Solo habrá
                //un director pero se deja preparado para proximas versiones que
                //soporten más de un director
                for($i=0;$i<count($directores);$i++){
                    $peliculaDirector->insert([
                        'id_pelicula' =>$id,
                        'id_director' =>$directores[$i]
                    ]);
                }

                //Introducimos los actores en PeliculasActor
                for($i=0;$i<count($actores);$i++){
                    $peliculaActor->insert([
                        'id_pelicula' =>$id,
                        'id_actor' =>$actores[$i]
                    ]);
                }
            //Mandamos la respuesta
            return $this->genericResponse($this->model->get($id), null, 200);
        }
     
        //Validacion de pelicula
        $validation = \Config\Services::validation();
        //Devolvemos error si no es validado
        return $this->genericResponse(null, $validation->getErrors(), 500);
    }
    
    
    //Metodo utilizado para las operaciones PUT/PATCH
    public function update($id = null)
    {
        $pelicula = new PeliculaModel();
    
        $data = $this->request->getRawInput();
     
        if ($this->validate('pelicula')) {
            if (!$pelicula->get($id)) {
                return $this->genericResponse(null, array("id" => "La pelicula no existe"), 500);
            }
     
            $pelicula->update($id, [
                    'titulo' => $data['titulo'],
                    'anyo' => $data['anyo'],
                    'duracion' => $data['duracion']
                ]);
     
            return $this->genericResponse($this->model->get($id), null, 200);
        }
    }

    //Metodo utilizado para las operaciones DELETE
    public function delete($id = null)
    {
        $pelicula = new PeliculaModel();
        //Comprobamos antes de borrar si existe
        if (!$this->model->get($id)) {
            return $this->genericResponse(null, array("id" => "La pelicula no existe"), 500);
        } else {
            $pelicula->delete($id);
            return $this->genericResponse("Pelicula eliminada", null, 200);
        }
    }
}
