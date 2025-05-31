<?php
/**
*    File        : backend/controllers/studentsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

/*Este archivo es un controlador. Es decir, define funciones que
se ejecutan cuando el usuario hace una operación sobre los
estudiantes: ver, crear, modificar o eliminar.
Las funciones que contiene se llaman desde routesFactory.php,
y usan funciones del modelo (ubicadas en models/students.php)
para acceder a la base de datos. */

require_once("./models/students.php");
/*Esta línea incluye el archivo que contiene las funciones para
trabajar con la tabla students en la base de datos.
● Esto permite usar funciones como getAllStudents,
createStudent, updateStudent, etc.
● Se usa require_once para evitar incluir el mismo archivo
más de una vez (lo que provocaría error). */

function handleGet($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    /*● Esta función se ejecuta cuando se hace una petición GET.
● Intenta leer el contenido del cuerpo de la petición
(php://input) y convertirlo desde JSON a un array de PHP
usando json_decode.

Aunque en GET no suele usarse el cuerpo del envío, sino que
viaja por URL, este proyecto lo permite para poder filtrar por
id. */
    
    if (isset($input['id'])) 
    {
        $student = getStudentById($conn, $input['id']);
        echo json_encode($student);
    } 
    /*Si el JSON recibido contiene un campo "id", se busca un único
estudiante con ese ID. Se usa la función del modelo
getStudentById(...). La respuesta se convierte nuevamente a
JSON usando json_encode. */

    else
    {
        $students = getAllStudents($conn);
        echo json_encode($students);
    }
    /*Si no se recibió ningún id, entonces se devuelven todos los
estudiantes. */
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    /*/*Se ejecuta cuando el cliente hace una petición POST.
El POST se usa para crear un nuevo estudiante.
Se convierte el JSON recibido en un array. */

    $result = createStudent($conn, $input['fullname'], $input['email'], $input['age']);
    /*Llama a la función createStudent(...), pasando los datos que
vienen del formulario: fullname, email, age. */

    if ($result['inserted'] > 0) 
    {
        echo json_encode(["message" => "Estudiante agregado correctamente"]);
    } 
    /*Si la creación fue exitosa (inserted > 0), se devuelve un mensaje
JSON de éxito. */

    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo agregar"]);
    }
    /*Si hubo un problema, se responde con:
● Código HTTP 500 (error del servidor).
● Un mensaje JSON de error. */
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    /*● Se ejecuta cuando el cliente hace una petición PUT
(actualización).
● Se leen los datos en formato JSON. */

    $result = updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age']);
    if ($result['updated'] > 0) 
    {
        echo json_encode(["message" => "Actualizado correctamente"]);
    } 
    /*Si la actualización del estudiante fue exitosa (updated > 0), se
devuelve un mensaje JSON de éxito. */
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
    /*Si algo falla, se responde con error HTTP 500. */
}

function handleDelete($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    /*● Se ejecuta cuando el cliente hace una petición DELETE.
● Lee el ID del estudiante a eliminar. */

    $result = deleteStudent($conn, $input['id']);
    if ($result['deleted'] > 0) 
    {
        echo json_encode(["message" => "Eliminado correctamente"]);
    } 
    /*Si se pudo eliminar el estudiante, se responde con un mensaje
de éxito. */
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
    /*Si ocurre un problema, se devuelve un mensaje de error. */
}
?>