<?php
/**
*    File        : backend/models/studentsSubjects.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

function assignSubjectToStudent($conn, $student_id, $subject_id, $approved) 
{
    $sql = "INSERT INTO students_subjects (student_id, subject_id, approved) VALUES (?, ?, ?)";
    /*Se prepara un INSERT con placeholders ? para evitar
inyecciones SQL. */

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $subject_id, $approved);
    /*Se enlazan los valores a los placeholders. El "iii" indica que
son tres enteros (i de integer). */

    $stmt->execute();

    return 
    [
        'inserted' => $stmt->affected_rows,        
        'id' => $conn->insert_id
    ];
    /*Devuelve la cantidad de filas insertadas y el ID del nuevo
registro. */
}

//Query escrita sin ALIAS resumidos (a mi me gusta más):
function getAllSubjectsStudents($conn) 
{
    $sql = "SELECT students_subjects.id,
                students_subjects.student_id,
                students_subjects.subject_id,
                students_subjects.approved,
                students.fullname AS student_fullname,
                subjects.name AS subject_name
            FROM students_subjects
            JOIN subjects ON students_subjects.subject_id = subjects.id
            JOIN students ON students_subjects.student_id = students.id";
            /*Esta consulta recupera todas las relaciones estudiante -
materia, junto con los nombres completos de estudiante y
materia, gracias a los JOIN. */

    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    /*Devuelve el resultado como array asociativo multidimensional,
listo para convertir a JSON. */
}

//Query escrita con ALIAS resumidos:
function getSubjectsByStudent($conn, $student_id) 
{
    $sql = "SELECT ss.subject_id, s.name, ss.approved
        FROM students_subjects ss
        JOIN subjects s ON ss.subject_id = s.id
        WHERE ss.student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result= $stmt->get_result();

    return $result->fetch_assoc(); 
}

function updateStudentSubject($conn, $id, $student_id, $subject_id, $approved) //Actualiza una relación existente con nuevos valores.
{
    $sql = "UPDATE students_subjects 
            SET student_id = ?, subject_id = ?, approved = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $student_id, $subject_id, $approved, $id);
    $stmt->execute();

    return ['updated' => $stmt->affected_rows]; //Devuelve cuántas filas fueron modificadas (idealmente 1).
}

function removeStudentSubject($conn, $id) //Elimina la relación estudiante-materia identificada por id.
{
    $sql = "DELETE FROM students_subjects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return ['deleted' => $stmt->affected_rows];
}

/*

assignSubjectToStudent() Crea nueva asignación (INSERT)
getAllSubjectsStudents() Devuelve todas las relaciones con nombres completos
getSubjectsByStudent() Devuelve materias de un solo estudiante (¡ver nota!)
updateStudentSubject() Modifica una relación existente
removeStudentSubject() Elimina una relación por su ID 

Notas pedagógicas
● Las funciones usan sentencias preparadas: buena práctica
de seguridad.
● Todas devuelven información útil para el controlador
(inserted, updated, deleted).
● fetch_assoc() en getSubjectsByStudent() puede limitar
resultados si hay más de una materia asignada.
● Las consultas JOIN permiten mostrar datos legibles en
frontend sin consultas adicionales.
*/
?>
