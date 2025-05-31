<!-- views/incidente/planes_accion.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planes de Acción del Incidente <?= $incidente['id'] ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include 'views/layout/header.php'; ?>
<div class="container mt-4">
    <h2>Planes de Acción para el Incidente ID: <?= $incidente['id'] ?></h2>
    <p><strong>Descripción del Incidente:</strong> <?= $incidente['descripcion'] ?></p>
    <!-- Botón para crear un nuevo plan de acción -->
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalCrearPlan">Crear Plan de Acción</button>
    <!-- Tabla de planes de acción -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Usuario Responsable</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($planesAccion as $plan): ?>
            <tr>
                <td><?= $plan['id'] ?></td>
                <td><?= $plan['descripcion'] ?></td>
                <td><?= $plan['usuario_nombre'] ?></td>
                <td><?= $plan['fecha_inicio'] ?></td>
                <td><?= $plan['fecha_fin'] ?></td>
                <td><?= $plan['estado_nombre'] ?></td>
                <td>
                    <!-- Botón para editar plan de acción -->
                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEditarPlan<?= $plan['id'] ?>">Editar</button>
                    <!-- Enlace para eliminar plan de acción -->
                    <a href="index.php?entity=incidente&action=planes_accion&id=<?= $incidente['id'] ?>&delete_plan=<?= $plan['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este plan de acción?')">Eliminar</a>
                </td>
            </tr>
            <!-- Modal para editar plan de acción -->
            <div class="modal fade" id="modalEditarPlan<?= $plan['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalEditarPlanLabel<?= $plan['id'] ?>" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <form action="index.php?entity=incidente&action=planes_accion&id=<?= $incidente['id'] ?>" method="POST">
                    <input type="hidden" name="action_plan" value="edit">
                    <input type="hidden" name="id_plan_accion" value="<?= $plan['id'] ?>">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modalEditarPlanLabel<?= $plan['id'] ?>">Editar Plan de Acción</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <!-- Campos del formulario -->
                      <div class="form-group">
                          <label for="descripcion">Descripción</label>
                          <textarea class="form-control" name="descripcion" required><?= $plan['descripcion'] ?></textarea>
                      </div>
                      <div class="form-group">
                          <label for="id_usuario">Usuario Responsable</label>
                          <select class="form-control" name="id_usuario" required>
                              <?php foreach ($usuarios as $usuario): ?>
                                  <option value="<?= $usuario['id'] ?>" <?= ($usuario['id'] == $plan['id_usuario']) ? 'selected' : '' ?>><?= $usuario['nombre'] ?></option>
                              <?php endforeach; ?>
                          </select>
                      </div>
                      <div class="form-group">
                          <label for="fecha_inicio">Fecha Inicio</label>
                          <input type="date" class="form-control" name="fecha_inicio" value="<?= $plan['fecha_inicio'] ?>" required>
                      </div>
                      <div class="form-group">
                          <label for="fecha_fin">Fecha Fin</label>
                          <input type="date" class="form-control" name="fecha_fin" value="<?= $plan['fecha_fin'] ?>" required>
                      </div>
                      <div class="form-group">
                          <label for="id_estado">Estado</label>
                          <select class="form-control" name="id_estado" required>
                              <?php foreach ($estados as $estado): ?>
                                  <option value="<?= $estado['id'] ?>" <?= ($estado['id'] == $plan['id_estado']) ? 'selected' : '' ?>><?= $estado['nombre'] ?></option>
                              <?php endforeach; ?>
                          </select>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Modal para crear plan de acción -->
    <div class="modal fade" id="modalCrearPlan" tabindex="-1" role="dialog" aria-labelledby="modalCrearPlanLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form action="index.php?entity=incidente&action=planes_accion&id=<?= $incidente['id'] ?>" method="POST">
            <input type="hidden" name="action_plan" value="create">
            <div class="modal-header">
              <h5 class="modal-title" id="modalCrearPlanLabel">Crear Plan de Acción</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <!-- Campos del formulario -->
              <div class="form-group">
                  <label for="descripcion">Descripción</label>
                  <textarea class="form-control" name="descripcion" required></textarea>
              </div>
              <div class="form-group">
                  <label for="id_usuario">Usuario Responsable</label>
                  <select class="form-control" name="id_usuario" required>
                      <?php foreach ($usuarios as $usuario): ?>
                          <option value="<?= $usuario['id'] ?>"><?= $usuario['nombre'] ?></option>
                      <?php endforeach; ?>
                  </select>
              </div>
              <div class="form-group">
                  <label for="fecha_inicio">Fecha Inicio</label>
                  <input type="date" class="form-control" name="fecha_inicio" required>
              </div>
              <div class="form-group">
                  <label for="fecha_fin">Fecha Fin</label>
                  <input type="date" class="form-control" name="fecha_fin" required>
              </div>
              <div class="form-group">
                  <label for="id_estado">Estado</label>
                  <select class="form-control" name="id_estado" required>
                      <?php foreach ($estados as $estado): ?>
                          <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
                      <?php endforeach; ?>
                  </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Crear</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Botón para regresar al listado de incidentes -->
    <a href="index.php?entity=incidente&action=index" class="btn btn-secondary">Volver a Incidentes</a>
</div>
<!-- Scripts de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>
</html>