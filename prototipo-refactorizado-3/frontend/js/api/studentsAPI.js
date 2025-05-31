/**
*    File        : frontend/js/api/studentsAPI.js
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

import { createAPI } from './apiFactory.js';
/*Importación de función: Aquí estamos trayendo (import) una
función llamada createAPI, que está definida en otro archivo:
apiFactory.js. Equivalente a pedir prestada una herramienta ya
construida para no tener que repetir código. */

export const studentsAPI = createAPI('students');
/*Exportación de objeto API: Estamos creando un objeto llamado
studentsAPI usando la función createAPI, y se lo pasamos el
string 'students' como nombre del módulo.
Esto genera automáticamente funciones para obtener, crear,
modificar y borrar estudiantes del backend, usando la URL:

../../backend/server.php?module=students
Exportamos studentsAPI para que pueda ser usado desde otros
archivos como studentsController.js. */
