<!-- views/layout/header.php - VERSIÓN CORREGIDA -->
<?php
// Definir $entity si no existe para evitar errores
$entity = $entity ?? ($_GET['entity'] ?? 'hallazgo');
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Gestión</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item <?= ($entity === 'hallazgo') ? 'active' : '' ?>">
                <a class="nav-link" href="index.php?entity=hallazgo&action=index">Hallazgos</a>
            </li>
            <li class="nav-item <?= ($entity === 'incidente') ? 'active' : '' ?>">
                <a class="nav-link" href="index.php?entity=incidente&action=index">Incidentes</a>
            </li>
        </ul>
    </div>
</nav>