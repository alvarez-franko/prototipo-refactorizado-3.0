<?php
/**
*    File        : backend/models/students.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

/*Este archivo define una serie de funciones PHP que ejecutan
consultas SQL sobre la tabla students. Cada función realiza
una acción típica de un CRUD:
● getAllStudents: Obtener todos los estudiantes.
● getStudentById: Obtener un estudiante por su ID.
● createStudent: Insertar un nuevo estudiante.
● updateStudent: Modificar un estudiante existente.
● deleteStudent: Eliminar un estudiante. */

function getAllStudents($conn) 
{
    $sql = "SELECT * FROM students";

    //MYSQLI_ASSOC devuelve un array ya listo para convertir en JSON:
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}
/*● Define una consulta SQL que selecciona todas las filas de
la tabla students.
● Usa $conn->query($sql) para ejecutar esa consulta con
MySQLi.
● Luego, fetch_all(MYSQLI_ASSOC) convierte el resultado en
un array asociativo (clave: nombre de columna, valor:
dato).
● Este array es fácil de convertir a JSON en el
controlador.

IMPORTANTE: MYSQLI_ASSOC significa que los datos vienen como:
[
    ["id" => 1, "fullname" => "Juan", ...],
    ["id" => 2, "fullname" => "María", ...],
] */

function getStudentById($conn, $id) 
{
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    //fetch_assoc() devuelve un array asociativo ya listo para convertir en JSON de una fila:
    return $result->fetch_assoc(); 
}
/*Explicación:
1. Prepara una consulta SQL con ? como marcador de posición.
2. bind_param("i", $id) le dice a PHP: "Reemplazá el ? con un
entero (i) que viene en $id".
3. execute() corre la consulta.
4. get_result() obtiene el resultado.
5. fetch_assoc() extrae una sola fila como array asociativo
(ideal cuando se busca por id). */

function createStudent($conn, $fullname, $email, $age) 
{
    $sql = "INSERT INTO students (fullname, email, age) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullname, $email, $age);
    $stmt->execute();

    //Se retorna un arreglo con la cantidad e filas insertadas 
    //y id insertado para validar en el controlador:
    return 
    [
        'inserted' => $stmt->affected_rows,        
        'id' => $conn->insert_id
    ];
}
/*Explicación:
1. Prepara una consulta INSERT para agregar un nuevo
estudiante.
2. bind_param("ssi", ...):
    ○ "s" = string (fullname)
    ○ "s" = string (email)
    ○ "i" = integer (age)
3. Ejecuta la consulta.
4. Devuelve en un array:
    ○ inserted: cuántas filas se insertaron (debería ser 1).
    ○ id: el id autogenerado por la base de datos. Este
    array es útil para confirmar el éxito de la
    operación en el controlador. */

function updateStudent($conn, $id, $fullname, $email, $age) 
{
    $sql = "UPDATE students SET fullname = ?, email = ?, age = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $fullname, $email, $age, $id);
    $stmt->execute();

    //Se retorna fila afectadas para validar en controlador:
    return ['updated' => $stmt->affected_rows];
}
/*Explicación:
1. Prepara una consulta UPDATE para modificar un estudiante
por su id.
2. bind_param("ssii", ...):
    ○ "s": fullname
    ○ "s": email
    ○ "i": age
    ○ "i": id
3. Ejecuta la consulta.
4. Devuelve cuántas filas fueron modificadas (updated). Si
affected_rows = 0, significa que el estudiante no existía
o que los datos eran iguales. */

function deleteStudent($conn, $id) 
{
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    //Se retorna fila afectadas para validar en controlador
    return ['deleted' => $stmt->affected_rows];
}
/*Explicación:
1. Prepara una consulta DELETE para borrar un estudiante por
su ID.
2. Se usa bind_param("i", $id) para evitar inyecciones SQL.
3. Ejecuta y devuelve cuántas filas fueron eliminadas.*/

/*📌 Este archivo representa la capa de acceso a datos (modelo)
de tu arquitectura.
    ● No tiene lógica de interfaz ni control de flujo.
    ● Cada función se conecta directamente con la base de datos
    MySQL usando MySQLi.
    ● Utiliza prepare() y bind_param() para evitar inyección
    SQL y manejar los tipos correctamente.
    ● El resultado de cada función se devuelve como un array de
    PHP, listo para ser convertido a JSON en los
    controladores. */
?>
