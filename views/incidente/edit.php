<!-- views/incidente/edit.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Incidente</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include 'views/layout/header.php'; ?>
<div class="container mt-4">
    <h1>Editar Incidente</h1>
    <form action="index.php?entity=incidente&action=edit&id=<?= $incidente['id'] ?>" method="POST">
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required><?= $incidente['descripcion'] ?></textarea>
        </div>
        <div class="form-group">
            <label for="fecha_ocurrencia">Fecha de Ocurrencia</label>
            <input type="date" class="form-control" id="fecha_ocurrencia" name="fecha_ocurrencia" value="<?= $incidente['fecha_ocurrencia'] ?>" required>
        </div>
        <div class="form-group">
            <label for="id_estado">Estado</label>
            <select class="form-control" id="id_estado" name="id_estado" required>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?= $estado['id'] ?>" <?= ($estado['id'] == $incidente['id_estado']) ? 'selected' : '' ?>>
                        <?= $estado['nombre'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_usuario">Usuario Responsable</label>
            <select class="form-control" id="id_usuario" name="id_usuario" required>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario['id'] ?>" <?= ($usuario['id'] == $incidente['id_usuario']) ? 'selected' : '' ?>>
                        <?= $usuario['nombre'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="index.php?entity=incidente&action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>