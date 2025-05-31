<!-- views/hallazgo/show.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Hallazgo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include 'views/layout/header.php'; ?>
<div class="container mt-4">
    <h1>Detalle del Hallazgo</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">ID: <?= $hallazgo['id'] ?></h5>
            <h6 class="card-subtitle mb-2 text-muted">Título: <?= $hallazgo['titulo'] ?></h6>
            <p class="card-text">Descripción: <?= $hallazgo['descripcion'] ?></p>
            <p class="card-text">Estado: <?= $hallazgo['estado_nombre'] ?></p>
            <p class="card-text">Usuario Responsable: <?= $hallazgo['usuario_nombre'] ?></p>
            <h6>Procesos Asociados:</h6>
            <ul>
                <?php foreach ($hallazgo['procesos'] as $proceso): ?>
                    <li><?= $proceso['nombre'] ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="index.php?entity=hallazgo&action=edit&id=<?= $hallazgo['id'] ?>" class="btn btn-warning">Editar</a>
            <a href="index.php?entity=hallazgo&action=delete&id=<?= $hallazgo['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este hallazgo?')">Eliminar</a>
            <a href="index.php?entity=hallazgo&action=index" class="btn btn-secondary">Volver a la lista</a>
        </div>
    </div>
</div>
</body>
</html>