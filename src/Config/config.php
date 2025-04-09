<?php
// src/Config/config.php

// --- ¡NO SUBIR ESTE ARCHIVO A GITHUB CON CREDENCIALES REALES! ---
// ---    MEJOR USAR VARIABLES DE ENTORNO (.env)               ---

define('DB_HOST', 'localhost');       // Usualmente 'localhost' con XAMPP
define('DB_NAME', 'clinica_empleados_db'); // El nombre de tu BD
define('DB_USER', 'root');            // Usuario por defecto de XAMPP
define('DB_PASS', '');                // Contraseña por defecto de XAMPP (vacía)
define('DB_CHARSET', 'utf8mb4');

// Opcional: URL base de tu aplicación (ayuda para generar links)
// Asegúrate que termine CON barra /
// Ejemplo: define('BASE_URL', 'http://localhost/tu-repositorio-clinica/public/');
define('BASE_URL', '/PARCIALDB/public/'); // Ruta relativa si XAMPP está configurado así

// Habilitar errores para desarrollo (¡desactivar en producción!)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>