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
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $hallazgo['titulo'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?= $hallazgo['descripcion'] ?></textarea>
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
            </div>
            
            <div class="col-md-6">
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
                
                <!-- CAMPO SEDE CORREGIDO -->
                <div class="form-group">
                    <label for="sede_id">Sede 🏢</label>
                    <select class="form-control" id="sede_id" name="sede_id">
                        <option value="">Sin sede asignada</option>
                        <?php if (isset($sedes) && !empty($sedes)): ?>
                            <?php foreach ($sedes as $sede): ?>
                                <option value="<?= $sede['id'] ?>" <?= ($sede['id'] == $hallazgo['sede_id']) ? 'selected' : '' ?>>
                                    <?= $sede['nombre'] ?> (<?= $sede['ciudad'] ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="form-text text-muted">
                        <?php if ($hallazgo['sede_nombre']): ?>
                            Actualmente asignado a: <strong><?= $hallazgo['sede_nombre'] ?></strong>
                        <?php else: ?>
                            Este hallazgo no tiene sede asignada
                        <?php endif; ?>
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="procesos">Procesos Relacionados</label>
                    <select multiple class="form-control" id="procesos" name="procesos[]" size="6">
                        <?php foreach ($procesos as $proceso): ?>
                            <option value="<?= $proceso['id'] ?>" <?= in_array($proceso['id'], $selectedProcesoIds) ? 'selected' : '' ?>>
                                <?= $proceso['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">Mantén presionada la tecla Ctrl para seleccionar múltiples procesos</small>
                </div>
            </div>
        </div>
        
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="index.php?entity=hallazgo&action=index" class="btn btn-secondary">Cancelar</a>
            <a href="index.php?entity=hallazgo&action=show&id=<?= $hallazgo['id'] ?>" class="btn btn-info">Ver Detalles</a>
        </div>
    </form>
</div>
</body>
</html>