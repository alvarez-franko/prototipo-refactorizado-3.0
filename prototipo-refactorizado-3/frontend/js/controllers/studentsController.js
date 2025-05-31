/**
*    File        : frontend/js/controllers/studentsController.js
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

/*Este archivo se encarga de:
● De manejar la lógica de vista del módulo de estudiantes.
● Importa y utiliza ../api/studentsAPI.js para acceder al
backend y poder crear, obtener, actualizar y borrar datos
del estudiante delegando esa funcionalidad a la API.
● Crea y actualiza el estado de todo elemento HTML
dinámicamente. */


import { studentsAPI } from '../api/studentsAPI.js';
/*● Esta línea importa las funciones del archivo
studentsAPI.js, que contiene las funciones para
comunicarse con el backend (crear, actualizar, eliminar y
obtener estudiantes).
● Esto permite usar studentsAPI.create(),
studentsAPI.update(), etc., dentro de este archivo. */

document.addEventListener('DOMContentLoaded', () => 
{
    loadStudents();
    setupFormHandler();
    setupCancelHandler();
});
/*Cuando el navegador termina de cargar toda la estructura de la
página (DOMContentLoaded), se ejecuta esta función:
● loadStudents() carga y muestra todos los estudiantes
actuales en la tabla.
● setupFormHandler() prepara el formulario para que cuando
el usuario haga clic en "Guardar", se capture y procese
la información.
● setupCancelHandler() configura el comportamiento del
botón “Cancelar”. */
  
function setupFormHandler() //configurar envío del formulario
{
    const form = document.getElementById('studentForm'); //Se busca el formulario con el id="studentForm" desde el HTML.
    form.addEventListener('submit', async e => 
    {
        e.preventDefault();
        /*Se configura el formulario para que, al enviarse, no recargue
la página (e.preventDefault() cancela el comportamiento por
defecto del navegador). */

        const student = getFormData();
        /*Se extraen los datos del formulario usando una función llamada
getFormData().*/
    
        try 
        {
            if (student.id) 
            {
                await studentsAPI.update(student);
            } 
            else 
            {
                await studentsAPI.create(student);
            }
            /*Dentro de un bloque Try (intenta el bloque y sino atrapa
una excepción, osea un error inesperado para el cual no fue
preparado el programa) hace lo siguiente: Si hay un ID (el
campo oculto está lleno), significa que se está editando un
estudiante → se llama update(). Si no hay ID, es un nuevo
estudiante → se llama create(). */

            clearForm();
            loadStudents();
        }
        catch (err)
        {
            console.error(err.message);
        }
    });
    /*Después de guardar, se limpian los campos del formulario
(clearForm()) y se recarga la tabla con loadStudents() para
ver los cambios. Si algo dentro del bloque try hubiese
“arrojado” una excepción, se atrapa en el bloque catch y se
envía por salida de error en la consola de javascript el
mensaje de error. */
}

function setupCancelHandler() //configurar la cancelación del formulario
{
    const cancelBtn = document.getElementById('cancelBtn');
    /*Se obtiene el elemento button (botón de cancelar) que tiene
id="cancelBtn". */

    cancelBtn.addEventListener('click', () => 
    {
        document.getElementById('studentId').value = '';
    });
    /*Cuando el botón se hace clic, se borra el valor del campo
oculto studentId. Esto asegura que, si el usuario había
seleccionado un estudiante para editar, al cancelar ya no se
intenta modificar nada por error. */
}
  
function getFormData() //obtener datos del formulario
{
    return {
        id: document.getElementById('studentId').value.trim(),
        fullname: document.getElementById('fullname').value.trim(),
        email: document.getElementById('email').value.trim(),
        age: parseInt(document.getElementById('age').value.trim(), 10)
    };
    /*Se crea un objeto (data object) JavaScript con los datos del
formulario (sería como una estructura de datos del formulario
para trabajar dentro del script):
● trim() elimina espacios en blanco innecesarios.
● parseInt(..., 10) convierte la edad a número entero (base
10). */
}
  
function clearForm() //limpiar el formulario
{
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').value = '';
    /*reset() limpia todos los campos del formulario.
Además, se borra manualmente el campo oculto studentId, para
que no se reutilice por error en un nuevo alta. */
}
  
async function loadStudents() //cargar estudiantes desde el backend
{
    try 
    {
        const students = await studentsAPI.fetchAll();
        renderStudentTable(students);
    } 
    catch (err) 
    {
        console.error('Error cargando estudiantes:', err.message);
    }
}
/*Se llama a studentsAPI.fetchAll() para obtener todos los
estudiantes desde el backend.
Luego se muestran en pantalla llamando a
renderStudentTable(students) pasando por parámetro los datos
de estudiantes recuperados de la API. */
  
function renderStudentTable(students) //mostrar estudiantes en una tabla
{
    const tbody = document.getElementById('studentTableBody');
    /*Se busca el cuerpo de la tabla donde se muestran los
estudiantes, y se eliminan las filas anteriores con
replaceChildren() para empezar desde cero. */

//qué filas anteriores? no entendí eso

    tbody.replaceChildren();
  
    students.forEach(student => 
    {
        const tr = document.createElement('tr');
        /*Para cada estudiante, se crea una nueva fila <tr>. */
    
        tr.appendChild(createCell(student.fullname));
        tr.appendChild(createCell(student.email));
        tr.appendChild(createCell(student.age.toString()));
        tr.appendChild(createActionsCell(student));
        /*Se crean celdas para nombre, email y edad, y una celda
adicional con los botones de acción (Editar y Borrar). */
    
        tbody.appendChild(tr);
        /*Finalmente, se agrega la fila a la tabla. */
    });
}
  
function createCell(text)
{
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}
/*Crea una celda (<td>) con el texto recibido.
Usa textContent para seguridad (no interpreta HTML, solo texto
plano así evita la ejecución de scripts maliciosos y el robo
de información de los usuarios bloqueando las entradas HTML. */
  
function createActionsCell(student) //botones editar y borrar
{
    const td = document.createElement('td');
    /*Se crea una celda que contendrá los botones de acción. */
  
    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => fillForm(student));
    /*Botón "Editar" con clase azul.
Al hacer clic, se llama a fillForm(student) para llenar el
formulario con los datos seleccionados. */
  
    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDelete(student.id));
    /*Botón "Borrar" con clase roja.
Al hacer clic, pide confirmación y, si el usuario acepta,
llama a confirmDelete(). */
  
    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
    /*Los botones se agregan a la celda y se retorna el elemento td. */
}
  
function fillForm(student) //cargar datos en el formulario
{
    document.getElementById('studentId').value = student.id;
    document.getElementById('fullname').value = student.fullname;
    document.getElementById('email').value = student.email;
    document.getElementById('age').value = student.age;
}
/*Esta función copia los datos del estudiante seleccionado en el
formulario, para permitir su edición. */
  
async function confirmDelete(id) //confirmar y borrar
{
    if (!confirm('¿Estás seguro que deseas borrar este estudiante?')) return;
    /*Muestra un cuadro de confirmación. Si el usuario cancela, no
se hace nada. */

    try 
    {
        await studentsAPI.remove(id);
        loadStudents();
    } 
    catch (err) 
    {
        console.error('Error al borrar:', err.message);
    }
    /**Si el usuario confirma, se llama a studentsAPI.remove() para
borrar en el backend, y se recarga la tabla. */
}
  