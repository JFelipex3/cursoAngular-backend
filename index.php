<?php

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli('localhost', 'root', '', 'curso_angular');

//Configuracion de cabeceras
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

//Listar todos los productos
$app -> get('/productos', function() use($db, $app){
    $sql = 'SELECT * FROM productos ORDER BY id DESC;';
    $query = $db -> query($sql);

    $productos = array();
    while ($producto = $query -> fetch_assoc()){
        $productos[] = $producto;
    }

    $result = array(
        'status' => 'success',
        'code' => 200,
        'data' => $productos
    );

    echo json_encode($result);
});

//Devolver un producto
$app -> get('/productos/:id', function($id) use($db, $app){
    $sql = 'SELECT * FROM productos WHERE id = '. $id .';';
    $query = $db -> query($sql);

    $result = array(
        'status' => 'error',
        'code' => 404,
        'message' => 'Producto no disponible'
    );

    if($query -> num_rows == 1){
        $producto = $query -> fetch_assoc();

        $result = array(
            'status' => 'success',
            'code' => 200,
            'data' => $producto
        );
    }

    echo json_encode($result);
});

//Eliminar un producto
$app -> get('/delete-producto/:id', function($id) use($db, $app){
    $sql = 'DELETE FROM productos WHERE id = '. $id;
    $query = $db -> query($sql);

    if($query){
        $result = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Producto se ha eliminado correctamente'
        );
    } else{
        $result = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'Producto no se ha eliminado'
        );
    }

    echo json_encode($result);
});

//Actualizar un producto
$app -> post('/update-producto/:id', function($id) use($db, $app){
   $json = $app -> request -> post('json');
   $data = json_decode($json, true);

    $sql = "UPDATE productos SET ". 
    "nombre = '{$data['nombre']}',".
    "description = '{$data['description']}',";

    if(isset($data['imagen'])){
        $sql .= "imagen = '{$data["imagen"]}', ";
    }

    $sql .= "precio = '{$data['precio']}' WHERE ID = ". $id .";";

    $query = $db -> query($sql);

    if($query){
        $result = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Producto se ha actualizado correctamente'
        );
    } else{
        $result = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'Producto no se ha actualizado'
        );
    }

    echo json_encode($result);
});

// subir imagen producto
$app -> post('/upload-file', function() use($db, $app){
    $result = array(
        'status' => 'error',
        'code' => 404,
        'message' => 'El archivo no se ha cargado'
    );

    if(isset($_FILES['uploads'])){
        $piramideUploader = new  PiramideUploader();

        $upload = $piramideUploader -> upload('image', "uploads", "uploads", array('image/jpeg', 'image/png', 'image/gif'));
        $file = $piramideUploader -> getInfoFile();
        $file_name = $file['complete_name'];

        if(isset($upload) && $upload["uploaded"] == false){
            $result = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El archivo no se ha cargado'
            );
        } else{
            $result = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'El archivo se ha cargado',
                'filename' => $file_name
            );
        }
    }

    echo json_encode($result);
});

//Guardar Productos
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
            'status' => 'success',
            'code' => 200,
            'message' => 'Producto creado correctamente'
        );
    }

    echo json_encode($result);
});

$app -> run();