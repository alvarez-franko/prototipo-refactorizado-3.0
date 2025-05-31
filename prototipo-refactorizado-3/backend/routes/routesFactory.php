<?php
/**
*    File        : backend/routes/routesFactory.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

/*Este archivo define una función reutilizable llamada
routeRequest(...) que se encarga de gestionar las rutas REST
(GET, POST, PUT, DELETE) y de conectar cada método HTTP con la
función que debe ejecutarse. */

function routeRequest($conn, $customHandlers = [], $prefix = 'handle') 
/*$conn Objeto de conexión a la base de datos (mysqli).

$customHandlers (opcional) Array de funciones
personalizadas para ciertos métodos HTTP.

$prefix (opcional) Prefijo que se usa para
construir los nombres por defecto de los
handlers. */
{
    $method = $_SERVER['REQUEST_METHOD'];
    /**Esta línea obtiene el método HTTP usado en la solicitud:
● Por ejemplo: "GET", "POST", "PUT" o "DELETE".
● PHP guarda automáticamente los datos del servidor en el
arreglo global $_SERVER. */

    // Lista de handlers CRUD por defecto
    $defaultHandlers = [
        'GET'    => $prefix . 'Get',
        'POST'   => $prefix . 'Post',
        'PUT'    => $prefix . 'Put',
        'DELETE' => $prefix . 'Delete'
    ];
    /*Se crea un array de funciones por defecto (handlers) asociadas
a cada método HTTP.
● Si el prefijo es 'handle', se espera que las funciones se
llamen:

○ handleGet($conn)
○ handlePost($conn)
○ handlePut($conn)
○ handleDelete($conn) */

    // Sobrescribir handlers por defecto si hay personalizados
    $handlers = array_merge($defaultHandlers, $customHandlers);
    /*Esta línea sobrescribe los handlers por defecto con versiones
personalizadas, si el usuario del módulo las definió.Por
ejemplo, en studentsRoutes.php, se personaliza POST, pero los
demás (GET, PUT, DELETE) usan los valores por defecto. */

    if (!isset($handlers[$method])) //Validar si el método está soportado
    {
        http_response_code(405);
        echo json_encode(["error" => "Método $method no permitido"]);
        return;
    }
    /*Si el método HTTP no está soportado (por ejemplo, un PATCH),
se responde con:
● Código HTTP 405 (Método no permitido).
● Un mensaje JSON explicando el error. */

    $handler = $handlers[$method];
    /*Esta línea guarda en $handler el nombre de la función que debe
ejecutarse para el método solicitado (handleGet, handlePost,
etc.), o bien la función anónima personalizada. */

    if (is_callable($handler)) 
    {
        $handler($conn);
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "Handler para $method no es válido"]);
    }
}
/*Se verifica si el valor de $handler realmente es una función
que se puede ejecutar.
● Si lo es, se llama a la función y se le pasa $conn.

Si el nombre del handler no corresponde a una función válida,
se devuelve un:
● Código HTTP 500 (Error interno del servidor).
● Mensaje JSON explicando que el handler es inválido. */
