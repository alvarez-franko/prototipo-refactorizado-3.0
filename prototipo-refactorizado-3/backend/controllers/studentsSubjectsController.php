<?php
/**
*    File        : backend/controllers/studentsSubjectsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

/*De igual comportamiento que los otros controladores. Se puede
deducir desde:
backend/controllers/studentsController.php */

require_once("./models/studentsSubjects.php");

function handleGet($conn) 
{
    $studentsSubjects = getAllSubjectsStudents($conn);
    echo json_encode($studentsSubjects);
}

function handlePost($conn)  
{
    $input = json_decode(file_get_contents("php://input"), true);

    // Validar datos obligatorios
    if (!isset($input['student_id'], $input['subject_id'], $input['approved'])) {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        return;
    }

    // Verificar si la relación ya existe
    $stmt = $conn->prepare("SELECT COUNT(*) FROM asignaciones WHERE student_id = :student_id AND subject_id = :subject_id");
    $stmt->bindParam(':student_id', $input['student_id']);
    $stmt->bindParam(':subject_id', $input['subject_id']);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        http_response_code(409); // Conflict
        echo json_encode(["error" => "La materia ya está asignada al estudiante."]);
        return;
    }

    // Intentar asignar la materia al estudiante
    $result = assignSubjectToStudent($conn, $input['student_id'], $input['subject_id'], $input['approved']);

    if ($result['inserted'] > 0) {
        echo json_encode(["message" => "Asignación realizada"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al asignar"]);
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'], $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        return;
    }

    $result = updateStudentSubject($conn, $input['id'], $input['student_id'], $input['subject_id'], $input['approved']);
    if ($result['updated'] > 0) 
    {
        echo json_encode(["message" => "Actualización correcta"]);
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

    $result = removeStudentSubject($conn, $input['id']);
    if ($result['deleted'] > 0) 
    {
        echo json_encode(["message" => "Relación eliminada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>
