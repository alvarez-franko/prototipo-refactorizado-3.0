<?php
/**
*    File        : backend/routes/studentsRoutes.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

require_once("./config/databaseConfig.php");
require_once("./routes/routesFactory.php");
require_once("./controllers/studentsController.php");
/*Usamos require_once para cargar y ejecutar otros archivos
necesarios:
1. databaseConfig.php: contiene los datos para conectarse a
la base de datos (usuario, contraseña, etc.).
2. routesFactory.php: contiene una función (routeRequest)
que gestiona las rutas generales para GET, POST, PUT,
DELETE.
3. studentsController.php: contiene funciones como
handleGet, handlePost, etc., que hacen el trabajo real
(consultar, insertar, modificar o borrar estudiantes).
Nota para estudiantes: require_once asegura que un archivo
solo se incluya una vez, evitando errores por duplicación de
funciones. */

// routeRequest($conn);


/**
 * Ejemplo de como se extiende un archivo de rutas 
 * para casos particulares
 * o validaciones:
 */
routeRequest($conn, [
    'POST' => function($conn) 
    {
        // Validación o lógica extendida
        $input = json_decode(file_get_contents("php://input"), true);
        if (empty($input['fullname'])) 
        {
            http_response_code(400);
            echo json_encode(["error" => "Falta el nombre"]);
            return;
        }
        handlePost($conn);
    }
]);

/*Esta es la parte clave del archivo.
Estamos usando la función routeRequest para definir una ruta
personalizada para las solicitudes POST (crear estudiante).
Esto extiende la lógica por defecto y nos permite agregar
validaciones propias.
Paso a paso:
1. routeRequest($conn, [...]):
Llama a la función routeRequest pasándole:
○ la conexión $conn a la base de datos
○ un array que contiene qué hacer en caso de ciertas
acciones HTTP (POST en este caso)

2. 'POST' => function($conn) { ... } Define lo que debe
pasar si el método HTTP es POST (crear nuevo estudiante).
3. json_decode(file_get_contents("php://input"), true); Lee
el cuerpo del mensaje HTTP, que viene en formato JSON, y
lo convierte en un array de PHP. php://input es una forma
de acceder al cuerpo bruto de la solicitud.
Esto se usa en solicitudes que no vienen de un formulario
clásico, como las que hace fetch() desde JavaScript. 
4. Si el campo fullname no está presente o está vacío, se
responde con un error HTTP 400 (bad request) y un mensaje en
JSON.
5. Si todo está bien:

handlePost($conn);
Se llama a la función handlePost() que está definida en el
archivo studentsController.php. Esta función se encarga de
guardar el estudiante en la base de datos.*/