/**
*    File        : frontend/js/api/apiFactory.js
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

/*Este archivo define la función genérica createAPI() que
permite crear objetos de acceso al backend para cualquier
módulo (students, subjects, etc.). */

export function createAPI(moduleName, config = {}) 
/*Se define y exporta una función llamada createAPI que recibe
dos parámetros:
● moduleName: el nombre del módulo (por ejemplo
'students').
● config: un objeto opcional (vacío por defecto) que
permite configurar opciones avanzadas si hace falta. */
{
    const API_URL = config.urlOverride ?? `../../backend/server.php?module=${moduleName}`;
    /*Construcción de la URL de acceso al servidor:
● Si config.urlOverride está definido, se usa ese valor
como URL personalizada.
● Si no, se construye una URL estándar apuntando a
server.php?module=nombre.
📌 El operador ?? (Operador de coalescencia nula) significa:
“si el valor de la izquierda es null o undefined, usar el de la
derecha”. */

    async function sendJSON(method, data) 
    /*Esta función auxiliar interna se usa para enviar datos al
servidor usando los métodos HTTP POST, PUT o DELETE. */
    {
        const res = await fetch(API_URL,
        {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        /*Se hace una petición fetch() al backend (method: GET):
● method: puede ser POST, PUT o DELETE.
● headers: indica que se está enviando JSON.
● body: convierte los datos a texto JSON antes de enviarlos
con JSON.stringify(data).
📌 El uso de await indica que se espera la respuesta antes de
seguir. */

        if (!res.ok) throw new Error(`Error en ${method}`);
        /*Si la respuesta del servidor no fue exitosa (res.ok es false),
se lanza un error indicando el tipo de operación que falló. */
        return await res.json();
        /*Si todo salió bien, se convierte la respuesta en un objeto
JavaScript usando res.json() y se devuelve. */
    }

    return {
        /*La función createAPI devuelve un objeto con 4 funciones que
representan las operaciones básicas del CRUD: */
        async fetchAll()
        {
            const res = await fetch(API_URL);
            if (!res.ok) throw new Error("No se pudieron obtener los datos");
            return await res.json();
        },
        /*Obtiene todos los registros del módulo desde el servidor.
● Usa fetch para hacer una petición GET simple.
● Si la respuesta no es válida, lanza un error.
● Devuelve los datos convertidos en objeto JSON. */

        async create(data)
        {
            return await sendJSON('POST', data);
        },
        /*Crea un nuevo registro en el backend (alta). Usa la función
auxiliar sendJSON con método POST. */

        async update(data)
        {
            return await sendJSON('PUT', data);
        },
        /*🔹 Actualiza un registro existente en el backend. Usa sendJSON
con método PUT. El parámetro data debe incluir el id del
registro que se desea actualizar. */

        async remove(id)
        {
            return await sendJSON('DELETE', { id });
        }
        /*Elimina un registro del backend, usando DELETE. Se pasa un
objeto { id } como contenido de la petición. */
    };
}
