<!-- views/incidente/show.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Incidente</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include 'views/layout/header.php'; ?>
<div class="container mt-4">
    <h1>Detalle del Incidente</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">ID: <?= $incidente['id'] ?></h5>
            <p class="card-text">Descripción: <?= $incidente['descripcion'] ?></p>
            <p class="card-text">Fecha de Ocurrencia: <?= $incidente['fecha_ocurrencia'] ?></p>
            <p class="card-text">Estado: <?= $incidente['estado_nombre'] ?></p>
            <p class="card-text">Usuario Responsable: <?= $incidente['usuario_nombre'] ?></p>
            <a href="index.php?entity=incidente&action=edit&id=<?= $incidente['id'] ?>" class="btn btn-warning">Editar</a>
            <a href="index.php?entity=incidente&action=delete&id=<?= $incidente['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este incidente?')">Eliminar</a>
            <a href="index.php?entity=incidente&action=index" class="btn btn-secondary">Volver a la lista</a>
        </div>
    </div>
</div>
</body>
</html>