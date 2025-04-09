<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Gestión Clínica' ?></title>
    <!-- Añade aquí tus CSS (Bootstrap, Tailwind, o CSS propio) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
    <style>
        /* Estilos básicos para ejemplo */
        body { font-family: sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions a { margin-right: 5px; text-decoration: none; }
        .actions .delete { color: red; }
        .actions .activate { color: green; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; }
        .alert-danger { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
        nav { background-color: #eee; padding: 10px; margin-bottom: 20px; }
        nav a { margin-right: 15px; }
        form label { display: block; margin-top: 10px; }
        form input, form select, form textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        form button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 15px; }
        form button.cancel { background-color: #6c757d; margin-left: 10px;}
    </style>
</head>
<body>
<nav>
    <a href="<?= BASE_URL ?>?action=index">Inicio (Listado Empleados)</a>
    <a href="<?= BASE_URL ?>?action=create">Nuevo Empleado</a>
    <!-- Añadir más enlaces si es necesario -->
</nav>
<main>
    <h1><?= $titulo ?? 'Gestión de Empleados' ?></h1>
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert <?= $_SESSION['mensaje_tipo'] ?? 'alert-info' ?>">
            <?= $_SESSION['mensaje'] ?>
            <?php
                // Limpiar mensaje después de mostrarlo
                unset($_SESSION['mensaje']);
                unset($_SESSION['mensaje_tipo']);
            ?>
        </div>
    <?php endif; ?>