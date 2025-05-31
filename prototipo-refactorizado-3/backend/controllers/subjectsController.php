<?php
/**
*    File        : backend/controllers/subjectsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

require_once("./models/subjects.php");

function handleGet($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['id'])) 
    {
        $subject = getSubjectById($conn, $input['id']);
        echo json_encode($subject);
    } 
    else 
    {
        $subjects = getAllSubjects($conn);
        echo json_encode($subjects);
    }
}

function handlePost($conn) {
    // Leer el cuerpo de la solicitud JSON
    $input = json_decode(file_get_contents("php://input"), true);

    // Validar que se haya recibido el nombre de la materia
    if (!isset($input['name']) || empty($input['name'])) {
        http_response_code(400);
        echo json_encode(["error" => "El nombre de la materia es obligatorio."]);
        return;
    }

    // Preparar la consulta para verificar si la materia ya existe
    $stmt = $conn->prepare("SELECT COUNT(*) FROM materias WHERE nombre = :nombre");
    $stmt->bindParam(':nombre', $input['name']);
    $stmt->execute();

    // Comprobar si la materia ya existe
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409); // Conflict
        echo json_encode(["error" => "La materia '{$input['name']}' ya existe."]);
        return;
    }

    // Preparar la consulta para insertar la nueva materia
    $stmt = $conn->prepare("INSERT INTO materias (nombre) VALUES (:nombre)");
    $stmt->bindParam(':nombre', $input['name']);

    // Ejecutar la inserción y verificar el resultado
    if ($stmt->execute()) {
        echo json_encode(["message" => "Materia '{$input['name']}' creada correctamente."]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo crear la materia."]);
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = updateSubject($conn, $input['id'], $input['name']);
    if ($result['updated'] > 0) 
    {
        echo json_encode(["message" => "Materia actualizada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    
    $result = deleteSubject($conn, $input['id']);
    if ($result['deleted'] > 0) 
    {
        echo json_encode(["message" => "Materia eliminada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>