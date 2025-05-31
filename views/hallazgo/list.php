<!-- views/hallazgo/list.php -->
<?php include 'views/layout/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Hallazgos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Lista de Hallazgos</h1>
    <a href="index.php?entity=hallazgo&action=create" class="btn btn-primary mb-3">Crear Hallazgo</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th>Procesos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hallazgos as $hallazgo): ?>
            <tr>
                <td><?= $hallazgo['id'] ?></td>
                <td><?= $hallazgo['titulo'] ?></td>
                <td><?= $hallazgo['descripcion'] ?></td>
                <td><?= $hallazgo['estado_nombre'] ?></td>
                <td><?= $hallazgo['usuario_nombre'] ?></td>
                <td>
                    <ul>
                        <?php foreach ($hallazgo['procesos'] as $proceso): ?>
                            <li><?= $proceso['nombre'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                </td>
                <td>
                    <a href="index.php?entity=hallazgo&action=show&id=<?= $hallazgo['id'] ?>" class="btn btn-info btn-sm">Ver</a>
                    <a href="index.php?entity=hallazgo&action=edit&id=<?= $hallazgo['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="index.php?entity=hallazgo&action=delete&id=<?= $hallazgo['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro?')">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>