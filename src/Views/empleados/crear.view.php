<?php
$titulo = "Registrar Nuevo Empleado";
require_once __DIR__ . '/../partials/header.view.php';
?>

<form action="<?= BASE_URL ?>?action=store" method="post">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required>

    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" name="apellido" required>

    <label for="dni">DNI:</label>
    <input type="text" id="dni" name="dni" required>

    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">

    <label for="genero">Género:</label>
    <select id="genero" name="genero">
        <option value="">Seleccionar...</option>
        <option value="Masculino">Masculino</option>
        <option value="Femenino">Femenino</option>
        <option value="Otro">Otro</option>
    </select>

    <label for="direccion">Dirección:</label>
    <textarea id="direccion" name="direccion"></textarea>

    <label for="telefono">Teléfono:</label>
    <input type="tel" id="telefono" name="telefono">

    <label for="email">Email:</label>
    <input type="email" id="email" name="email">

    <label for="fecha_ingreso">Fecha de Ingreso:</label>
    <input type="date" id="fecha_ingreso" name="fecha_ingreso" required>

    <label for="salario">Salario:</label>
    <input type="number" id="salario" name="salario" step="0.01" min="0" value="0.00">

    <label for="id_cargo">Cargo:</label>
    <select id="id_cargo" name="id_cargo" required>
        <option value="">Seleccionar Cargo...</option>
        <?php foreach ($cargos as $cargo): ?>
            <option value="<?= $cargo->id_cargo ?>"><?= htmlspecialchars($cargo->nombre_cargo) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="id_departamento">Departamento:</label>
    <select id="id_departamento" name="id_departamento">
        <option value="">Seleccionar Departamento (Opcional)...</option>
         <?php foreach ($departamentos as $departamento): ?>
            <option value="<?= $departamento->id_departamento ?>"><?= htmlspecialchars($departamento->nombre_departamento) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Guardar Empleado</button>
     <a href="<?= BASE_URL ?>?action=index" class="cancel button-like">Cancelar</a> <!-- Estilizar como botón si es necesario -->

</form>

<?php require_once __DIR__ . '/../partials/footer.view.php'; ?>