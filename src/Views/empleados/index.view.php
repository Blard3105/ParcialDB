<?php
$titulo = "Listado de Empleados";
require_once __DIR__ . '/../partials/header.view.php';
?>

<form method="get" action="<?= BASE_URL ?>">
    <input type="hidden" name="action" value="index">
    <label for="filtro_estado">Filtrar por Estado:</label>
    <select name="estado" id="filtro_estado" onchange="this.form.submit()">
        <option value="Activo" <?= ($estadoFiltro === 'Activo') ? 'selected' : '' ?>>Activos</option>
        <option value="Inactivo" <?= ($estadoFiltro === 'Inactivo') ? 'selected' : '' ?>>Inactivos</option>
        <option value="Todos" <?= ($estadoFiltro === 'Todos') ? 'selected' : '' ?>>Todos</option>
    </select>
    <noscript><button type="submit">Filtrar</button></noscript>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>DNI</th>
            <th>Cargo</th>
            <th>Departamento</th>
            <th>Fecha Ingreso</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($empleados)): ?>
            <?php foreach ($empleados as $empleado): ?>
                <tr>
                    <td><?= htmlspecialchars($empleado->id_empleado) ?></td>
                    <td><?= htmlspecialchars($empleado->nombre) ?></td>
                    <td><?= htmlspecialchars($empleado->apellido) ?></td>
                    <td><?= htmlspecialchars($empleado->dni) ?></td>
                    <td><?= htmlspecialchars($empleado->nombre_cargo ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($empleado->nombre_departamento ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($empleado->fecha_ingreso) ?></td>
                    <td><?= htmlspecialchars($empleado->estado) ?></td>
                    <td class="actions">
                        <a href="<?= BASE_URL ?>?action=edit&id=<?= $empleado->id_empleado ?>">Editar</a>
                        <?php if ($empleado->estado === 'Activo'): ?>
                            <a href="#" onclick="confirmarAccion('¿Estás seguro de que quieres desactivar a este empleado?', '<?= BASE_URL ?>?action=toggleStatus&id=<?= $empleado->id_empleado ?>')" class="delete">Desactivar</a>
                        <?php else: ?>
                            <a href="#" onclick="confirmarAccion('¿Estás seguro de que quieres activar a este empleado?', '<?= BASE_URL ?>?action=toggleStatus&id=<?= $empleado->id_empleado ?>')" class="activate">Activar</a>
                        <?php endif; ?>
                        <!-- Añadir enlace a Ver Detalles si se implementa -->
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No se encontraron empleados <?= htmlspecialchars($estadoFiltro !== 'Todos' ? 'con el estado '.strtolower($estadoFiltro) : '') ?>.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../partials/footer.view.php'; ?>