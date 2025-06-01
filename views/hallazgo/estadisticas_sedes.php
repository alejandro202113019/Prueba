<!-- views/hallazgo/estadisticas_sedes.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas por Sede</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'views/layout/header.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📊 Estadísticas de Hallazgos por Sede</h1>
        <a href="index.php?entity=hallazgo&action=index" class="btn btn-secondary">← Volver a Hallazgos</a>
    </div>

    <!-- Resumen General -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📈 Resumen General</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <?php 
                        $totalHallazgos = array_sum(array_column($estadisticas, 'total_hallazgos'));
                        $totalAbiertos = array_sum(array_column($estadisticas, 'hallazgos_abiertos'));
                        $totalCerrados = array_sum(array_column($estadisticas, 'hallazgos_cerrados'));
                        ?>
                        <div class="col-md-3">
                            <h3 class="text-primary"><?= $totalHallazgos ?></h3>
                            <p class="text-muted">Total Hallazgos</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-danger"><?= $totalAbiertos ?></h3>
                            <p class="text-muted">Hallazgos Abiertos</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-success"><?= $totalCerrados ?></h3>
                            <p class="text-muted">Hallazgos Cerrados</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-info"><?= count($estadisticas) ?></h3>
                            <p class="text-muted">Sedes Activas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Detalladas -->
    <div class="row">
        <!-- Tabla de Estadísticas -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">🏢 Detalle por Sede</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Sede</th>
                                    <th>Ciudad</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Abiertos</th>
                                    <th class="text-center">En Proceso</th>
                                    <th class="text-center">Resueltos</th>
                                    <th class="text-center">Cerrados</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estadisticas as $stat): ?>
                                <tr>
                                    <td><strong><?= $stat['sede_nombre'] ?></strong></td>
                                    <td><?= $stat['ciudad'] ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-primary"><?= $stat['total_hallazgos'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-danger"><?= $stat['hallazgos_abiertos'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning"><?= $stat['hallazgos_en_proceso'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success"><?= $stat['hallazgos_resueltos'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary"><?= $stat['hallazgos_cerrados'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="index.php?entity=hallazgo&action=index&sede_id=<?= $stat['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Ver hallazgos de esta sede">
                                            👁️
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Dona -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📊 Distribución por Sede</h5>
                </div>
                <div class="card-body">
                    <canvas id="sedeChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Barras -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📊 Estados de Hallazgos por Sede</h5>
                </div>
                <div class="card-body">
                    <canvas id="estadosChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Datos para los gráficos
const estadisticas = <?= json_encode($estadisticas) ?>;

// Gráfico de Dona - Distribución de hallazgos por sede
const sedeLabels = estadisticas.map(s => s.sede_nombre);
const sedeTotales = estadisticas.map(s => parseInt(s.total_hallazgos));

const sedeChart = new Chart(document.getElementById('sedeChart'), {
    type: 'doughnut',
    data: {
        labels: sedeLabels,
        datasets: [{
            data: sedeTotales,
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = sedeTotales.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Gráfico de Barras - Estados por sede
const estadosChart = new Chart(document.getElementById('estadosChart'), {
    type: 'bar',
    data: {
        labels: sedeLabels,
        datasets: [
            {
                label: 'Abiertos',
                data: estadisticas.map(s => parseInt(s.hallazgos_abiertos)),
                backgroundColor: '#dc3545'
            },
            {
                label: 'En Proceso',
                data: estadisticas.map(s => parseInt(s.hallazgos_en_proceso)),
                backgroundColor: '#ffc107'
            },
            {
                label: 'Resueltos',
                data: estadisticas.map(s => parseInt(s.hallazgos_resueltos)),
                backgroundColor: '#28a745'
            },
            {
                label: 'Cerrados',
                data: estadisticas.map(s => parseInt(s.hallazgos_cerrados)),
                backgroundColor: '#6c757d'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                stacked: true
            },
            y: {
                stacked: true,
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'top'
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        }
    }
});
</script>
</body>
</html>