/**
*    File        : frontend/js/controllers/studentsSubjectsController.js
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

import { studentsAPI } from '../api/studentsAPI.js';
import { subjectsAPI } from '../api/subjectsAPI.js';
import { studentsSubjectsAPI } from '../api/studentsSubjectsAPI.js';
/*Se importan las funciones necesarias para acceder a:
● Estudiantes (studentsAPI)
● Materias (subjectsAPI)
● Relaciones entre ellos (studentsSubjectsAPI) */

document.addEventListener('DOMContentLoaded', () => 
{
    initSelects();
    setupFormHandler();
    setupCancelHandler();
    loadRelations();
});
/*Al cargarse el DOM:
1. Se cargan los <select> con estudiantes y materias.
2. Se configura el evento submit del formulario.
3. Se configura el botón Cancelar.
4. Se carga la tabla de relaciones actuales. */

async function initSelects() //Carga estudiantes y materias en sus respectivos <select>.
{
    try 
    {
        // Cargar estudiantes
        const students = await studentsAPI.fetchAll();
        const studentSelect = document.getElementById('studentIdSelect');
        /*Cada estudiante se agrega como:
<option value="ID">Nombre</option>, igual para materias.

option.value = s.id;
option.textContent = s.fullname;
Si hay error, se captura con try/catch y se muestra en
consola. */

        students.forEach(s => 
        {
            const option = document.createElement('option');
            option.value = s.id;
            option.textContent = s.fullname;
            studentSelect.appendChild(option);
        });

        // Cargar materias
        const subjects = await subjectsAPI.fetchAll();
        const subjectSelect = document.getElementById('subjectIdSelect');
        subjects.forEach(sub => 
        {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.name;
            subjectSelect.appendChild(option);
        });
    } 
    catch (err) 
    {
        console.error('Error cargando estudiantes o materias:', err.message);
    }
}

function setupFormHandler() //Maneja el envío del formulario.
{
    const form = document.getElementById('relationForm');
    form.addEventListener('submit', async e => 
    {
        e.preventDefault();

        const relation = getFormData();

        try 
        {
            if (relation.id) 
            {
                await studentsSubjectsAPI.update(relation);
            } 
            else 
            {
                await studentsSubjectsAPI.create(relation);
            }
            clearForm();
            loadRelations();
        } 
        catch (err) 
        {
            console.error('Error guardando relación:', err.message);
        }
    });
    /*● Llama a getFormData() para armar el objeto { id,
student_id, subject_id, approved }.
● Si hay id, hace update; si no, hace create.
● Luego limpia el formulario y recarga la tabla. */
}

function setupCancelHandler() //Resetea el formulario y limpia el campo oculto relationId.
{
    const cancelBtn = document.getElementById('cancelBtn');
    cancelBtn.addEventListener('click', () => 
    {
        document.getElementById('relationId').value = '';
    });
}

function getFormData() 
{
    return{
        id: document.getElementById('relationId').value.trim(),
        student_id: document.getElementById('studentIdSelect').value,
        subject_id: document.getElementById('subjectIdSelect').value,
        approved: document.getElementById('approved').checked ? 1 : 0
    };
}
/*Extrae y devuelve un objeto javascript con los valores
actuales del formulario.

approved: document.getElementById('approved').checked ? 1 : 0

Convierte el checkbox a entero (1 o 0), para facilitar la
compatibilidad con el backend (MySQL no tiene true/false). */

function clearForm() //Resetea todos los campos del formulario y limpia el campo oculto relationId.
{
    document.getElementById('relationForm').reset();
    document.getElementById('relationId').value = '';
}

async function loadRelations() 
{
    try 
    {
        const relations = await studentsSubjectsAPI.fetchAll();
        
        /**
         * DEBUG
         */
        //console.log(relations);

        /**
         * En JavaScript: Cualquier string que no esté vacío ("") es considerado truthy.
         * Entonces "0" (que es el valor que llega desde el backend) es truthy,
         * ¡aunque conceptualmente sea falso! por eso: 
         * Se necesita convertir ese string "0" a un número real 
         * o asegurarte de comparar el valor exactamente. 
         * Con el siguiente código se convierten todos los string approved a enteros.
         */
        relations.forEach(rel => 
        {
            rel.approved = Number(rel.approved);
        });
        
        renderRelationsTable(relations);
    } 
    catch (err) 
    {
        console.error('Error cargando inscripciones:', err.message);
    }
}
/*Carga desde el backend la lista completa de relaciones.
const relations = await studentsSubjectsAPI.fetchAll();
Luego convierte el campo approved a número real, porque el
backend lo envía como cadena "0" o "1":
relations.forEach(rel => rel.approved = Number(rel.approved));
Esto evita el bug de que "0" (string) sea considerado truthy. */

function renderRelationsTable(relations) 
{
    const tbody = document.getElementById('relationTableBody');
    tbody.replaceChildren();

    relations.forEach(rel => 
    {
        const tr = document.createElement('tr');

        tr.appendChild(createCell(rel.student_fullname));
        tr.appendChild(createCell(rel.subject_name));
        tr.appendChild(createCell(rel.approved ? 'Sí' : 'No'));
        tr.appendChild(createActionsCell(rel));

        tbody.appendChild(tr);
    });
}
/*Limpia y reconstruye el <tbody> de la tabla usando DOM seguro
(createElement).
tr.appendChild(createCell(rel.student_fullname));
tr.appendChild(createCell(rel.subject_name));
tr.appendChild(createCell(rel.approved ? 'Sí' : 'No'));
● Muestra "Sí"/"No" para el campo approved.
● Agrega celda con botones de acción (Editar, Borrar). */

function createCell(text) 
{
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}

function createActionsCell(relation) 
{
    const td = document.createElement('td');

    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => fillForm(relation));

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDelete(relation.id));

    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}
/*Crea los botones de Editar y Borrar, y asigna sus eventos.
editBtn.addEventListener('click', () => fillForm(relation));
deleteBtn.addEventListener('click', () =>
confirmDelete(relation.id)); */

function fillForm(relation) 
{
    document.getElementById('relationId').value = relation.id;
    document.getElementById('studentIdSelect').value = relation.student_id;
    document.getElementById('subjectIdSelect').value = relation.subject_id;
    document.getElementById('approved').checked = !!relation.approved;
}
/*Llena el formulario con los datos de una relación seleccionada
para edición.
document.getElementById('approved').checked = !!relation.approved;
!!relation.approved convierte el número 0/1 a false/true. */

async function confirmDelete(id) 
{
    if (!confirm('¿Estás seguro que deseas borrar esta inscripción?')) return;

    try 
    {
        await studentsSubjectsAPI.remove(id);
        loadRelations();
    } 
    catch (err) 
    {
        console.error('Error al borrar inscripción:', err.message);
    }
}
/*Confirma el borrado con window.confirm(), y si el usuario
acepta, llama a studentsSubjectsAPI.remove(id). */


/*Comentarios pedagógicos

Este controlador está especialmente diseñado para enseñar:
● Buen uso de async/await y fetch con APIs modulares.
● Manipulación segura del DOM sin innerHTML.
● Conversión entre tipos para evitar bugs comunes ("0" vs
0).
● Separación de responsabilidades clara y mantenible. */
