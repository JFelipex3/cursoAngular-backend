<?php

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli('localhost', 'root', '', 'curso_angular');

$app -> get("/pruebas", function() use($app, $db){
    echo "Hola mundo desde Slim";
    var_dump($db);
});

$app -> get("/probando", function() use($app){
    echo "Otro Texto";
});

$app -> post('/productos', function() use($app, $db){
    $json = $app -> request -> post('json');
    $data = json_decode($json, true);

    if(!isset($data['nombre'])){
        $data['nombre'] = null;
    }

    if(!isset($data['description'])){
        $data['description'] = null;
    }

    if(!isset($data['precio'])){
        $data['precio'] = null;
    }

    if(!isset($data['imagen'])){
        $data['imagen'] = null;
    }

    $query = "INSERT INTO productos VALUES (NULL,".
             "'{$data['nombre']}',".
             "'{$data['description']}',".
             "'{$data['precio']}',".
             "'{$data['imagen']}'".
             ");";

    $insert = $db -> query($query);

    if($insert){
        $result = array(
            "status" => 'success',
            "code" => 200,
            "message" => 'Producto creado correctamente'
        );
    }

    echo json_encode($result);
});

$app -> run();