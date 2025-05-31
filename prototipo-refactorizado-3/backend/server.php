<?php
/**
*    File        : backend/server.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

/**FOR DEBUG: */
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

/*Este archivo es el punto de entrada principal al backend. Se
encarga de recibir las solicitudes desde el frontend y
redirigirlas al módulo correspondiente (por ejemplo, students,
subjects, etc.). */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
/*Estas tres líneas son fundamentales cuando el frontend
(JavaScript) está separado del backend (PHP).
1. Access-Control-Allow-Origin: * ➤ Permite que cualquier
sitio web se comunique con este servidor. (En producción,
se suele reemplazar * por una dirección específica.)
2. Access-Control-Allow-Methods: GET, POST, PUT, DELETE,
OPTIONS ➤ Indica qué métodos HTTP se permiten. Son las
acciones básicas del CRUD.
3. Access-Control-Allow-Headers: Content-Type ➤ Permite que
las solicitudes incluyan el tipo de contenido (por
ejemplo, JSON).
🧠Esto se conoce como configuración CORS (Cross-Origin
Resource Sharing). */

function sendCodeMessage($code, $message = "") //Función auxiliar para responder con código HTTP
{
    http_response_code($code);
    echo json_encode(["message" => $message]);
    exit();
}
/*Esta función sirve para enviar una respuesta HTTP
personalizada al frontend.
● http_response_code($code): envía un código de estado HTTP
(como 200, 400, 404).
● echo json_encode(...): convierte un mensaje PHP en texto
JSON para que lo entienda el navegador.
● exit(): detiene el script inmediatamente. */

// Respuesta correcta para solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
{
    sendCodeMessage(200); // 200 OK
}
/*El navegador a veces envía primero una solicitud de
prueba llamada OPTIONS para verificar si puede hablar con
el servidor. Si la solicitud es OPTIONS, respondemos con
un código 200 y no hacemos más nada. */

// Obtener el módulo desde la query string
$uri = parse_url($_SERVER['REQUEST_URI']);
$query = $uri['query'] ?? '';
parse_str($query, $query_array);
$module = $query_array['module'] ?? null;
/*Estas líneas extraen el nombre del módulo desde la URL del
navegador.
Por ejemplo para la URL: server.php?module=students

1. $_SERVER['REQUEST_URI']: contiene la ruta completa que
pidió el navegador.
2. parse_url(...): separa la parte ?module=students.
3. parse_str(...): convierte el string en un array
asociativo.
4. Resultado: $query_array['module'] tendrá el valor
'students'./*/



// Validación de existencia del módulo
if (!$module)
{
    sendCodeMessage(400, "Módulo no especificado");
}
    /*Si no se especificó el parámetro module, respondemos con un
error 400 usando la función auxiliar: sendCodeMessage.*/



// Validación de caracteres seguros: solo letras, números y guiones bajos
if (!preg_match('/^\w+$/', $module))
{
    sendCodeMessage(400, "Nombre de módulo inválido");
}
   /* Si el nombre del módulo contiene caracteres no permitidos,
también damos error.
● La expresión regular ^\w+$ significa: solo letras,
números y guiones bajos.
● Esto protege contra inyecciones de código o rutas
maliciosas.*/



// Buscar el archivo de ruta correspondiente
$routeFile = __DIR__ . "/routes/{$module}Routes.php";
/*Construimos el path del archivo PHP que maneja ese módulo.Por
ejemplo, si $module es 'students', entonces el archivo es:
backend/routes/studentsRoutes.php.
__DIR__ es una constante que contiene la ruta de esta carpeta
(backend/).*/



if (file_exists($routeFile))
{
    require_once($routeFile);
}
else
{
    sendCodeMessage(404, "Ruta para el módulo '{$module}' no encontrada");
}
   /* Si el archivo existe, lo cargamos y ejecutamos con
require_once. Ese archivo será responsable de manejar el GET,
POST, PUT, DELETE del módulo. Si el archivo no existe,
enviamos un error 404 Not Found.



¿Qué hace server.php?
1. Recibe la solicitud del navegador o frontend.
2. Valida si el módulo es correcto.
3. Redirige la petición al archivo PHP adecuado según el
módulo.
4. Maneja solicitudes OPTIONS automáticamente para CORS.
5. Si algo falla, responde con un mensaje de error en JSON.
