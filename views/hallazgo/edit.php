<!-- views/hallazgo/edit.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Hallazgo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include 'views/layout/header.php'; ?>
<div class="container mt-4">
    <h1>Editar Hallazgo</h1>
    <form action="index.php?entity=hallazgo&action=edit&id=<?= $hallazgo['id'] ?>" method="POST">
        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $hallazgo['titulo'] ?>" required>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required><?= $hallazgo['descripcion'] ?></textarea>
        </div>
        <div class="form-group">
            <label for="id_estado">Estado</label>
            <select class="form-control" id="id_estado" name="id_estado" required>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?= $estado['id'] ?>" <?= ($estado['id'] == $hallazgo['id_estado']) ? 'selected' : '' ?>>
                        <?= $estado['nombre'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_usuario">Usuario Responsable</label>
            <select class="form-control" id="id_usuario" name="id_usuario" required>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario['id'] ?>" <?= ($usuario['id'] == $hallazgo['id_usuario']) ? 'selected' : '' ?>>
                        <?= $usuario['nombre'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="procesos">Procesos</label>
            <select multiple class="form-control" id="procesos" name="procesos[]">
                <?php foreach ($procesos as $proceso): ?>
                    <option value="<?= $proceso['id'] ?>" <?= in_array($proceso['id'], $selectedProcesoIds) ? 'selected' : '' ?>>
                        <?= $proceso['nombre'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="index.php?entity=hallazgo&action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
