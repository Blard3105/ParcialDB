<?php
$titulo = "Editar Empleado: " . htmlspecialchars($empleado->nombre) . " " . htmlspecialchars($empleado->apellido);
require_once __DIR__ . '/../partials/header.view.php';
?>

<form action="<?= BASE_URL ?>?action=update" method="post">
    <input type="hidden" name="id_empleado" value="<?= htmlspecialchars($empleado->id_empleado) ?>">

    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($empleado->nombre) ?>" required>

    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($empleado->apellido) ?>" required>

    <label for="dni">DNI:</label>
    <input type="text" id="dni" name="dni" value="<?= htmlspecialchars($empleado->dni) ?>" required>

    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($empleado->fecha_nacimiento ?? '') ?>">

    <label for="genero">Género:</label>
    <select id="genero" name="genero">
        <option value="">Seleccionar...</option>
        <option value="Masculino" <?= ($empleado->genero === 'Masculino') ? 'selected' : '' ?>>Masculino</option>
        <option value="Femenino" <?= ($empleado->genero === 'Femenino') ? 'selected' : '' ?>>Femenino</option>
        <option value="Otro" <?= ($empleado->genero === 'Otro') ? 'selected' : '' ?>>Otro</option>
    </select>

    <label for="direccion">Dirección:</label>
    <textarea id="direccion" name="direccion"><?= htmlspecialchars($empleado->direccion ?? '') ?></textarea>

    <label for="telefono">Teléfono:</label>
    <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($empleado->telefono ?? '') ?>">

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($empleado->email ?? '') ?>">

    <label for="fecha_ingreso">Fecha de Ingreso:</label>
    <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?= htmlspecialchars($empleado->fecha_ingreso) ?>" required>

    <label for="salario">Salario:</label>
    <input type="number" id="salario" name="salario" step="0.01" min="0" value="<?= htmlspecialchars($empleado->salario) ?>">

    <label for="id_cargo">Cargo:</label>
    <select id="id_cargo" name="id_cargo" required>
        <option value="">Seleccionar Cargo...</option>
        <?php foreach ($cargos as $cargo): ?>
            <option value="<?= $cargo->id_cargo ?>" <?= ($empleado->id_cargo == $cargo->id_cargo) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cargo->nombre_cargo) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="id_departamento">Departamento:</label>
    <select id="id_departamento" name="id_departamento">
        <option value="">Seleccionar Departamento (Opcional)...</option>
         <?php foreach ($departamentos as $departamento): ?>
             <option value="<?= $departamento->id_departamento ?>" <?= ($empleado->id_departamento == $departamento->id_departamento) ? 'selected' : '' ?>>
                <?= htmlspecialchars($departamento->nombre_departamento) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <p><strong>Estado Actual:</strong> <?= htmlspecialchars($empleado->estado) ?></p>
    <!-- El estado se cambia con la acción 'toggleStatus', no aquí -->


    <button type="submit">Actualizar Empleado</button>
    <a href="<?= BASE_URL ?>?action=index" class="cancel button-like">Cancelar</a>

</form>

<?php require_once __DIR__ . '/../partials/footer.view.php'; ?>