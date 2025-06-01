<!-- views/hallazgo/create.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Hallazgo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include 'views/layout/header.php'; ?>
<div class="container mt-4">
    <h1>Crear Hallazgo</h1>
    <form action="index.php?entity=hallazgo&action=create" method="POST">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="id_estado">Estado</label>
                    <select class="form-control" id="id_estado" name="id_estado" required>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_usuario">Usuario Responsable</label>
                    <select class="form-control" id="id_usuario" name="id_usuario" required>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= $usuario['id'] ?>"><?= $usuario['nombre'] ?></option>
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
                                <option value="<?= $sede['id'] ?>">
                                    <?= $sede['nombre'] ?> (<?= $sede['ciudad'] ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="form-text text-muted">Selecciona la sede donde ocurrió el hallazgo</small>
                </div>
                
                <div class="form-group">
                    <label for="procesos">Procesos Relacionados</label>
                    <select multiple class="form-control" id="procesos" name="procesos[]" size="6">
                        <?php foreach ($procesos as $proceso): ?>
                            <option value="<?= $proceso['id'] ?>"><?= $proceso['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">Mantén presionada la tecla Ctrl para seleccionar múltiples procesos</small>
                </div>
            </div>
        </div>
        
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Guardar Hallazgo</button>
            <a href="index.php?entity=hallazgo&action=index" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>