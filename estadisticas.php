<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "guardian_aire_db");
if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

// Conteo por tipo
$sql = "SELECT t.nombre, COUNT(r.id_reporte) as total 
        FROM tipo_problema t 
        LEFT JOIN reporte r ON t.id_tipo = r.tipo_id 
        GROUP BY t.id_tipo";
$result = $conn->query($sql);
$stats = ['quemas' => 0, 'tala' => 0, 'basura' => 0, 'olor' => 0];
while ($row = $result->fetch_assoc()) {
    switch ($row['nombre']) {
        case 'Quema': $stats['quemas'] = $row['total']; break;
        case 'Tala': $stats['tala'] = $row['total']; break;
        case 'Basura': $stats['basura'] = $row['total']; break;
        case 'Mal olor': $stats['olor'] = $row['total']; break;
    }
}

// Reportes de esta semana (desde el lunes)
$semanaInicio = date('Y-m-d', strtotime('monday this week'));
$sqlSemana = "SELECT COUNT(*) as total FROM reporte WHERE fecha_hora >= '$semanaInicio'";
$resultSemana = $conn->query($sqlSemana);
$rowSemana = $resultSemana->fetch_assoc();
$stats['esta_semana'] = $rowSemana['total'];

// Tendencia: comparar semana actual vs anterior
$semanaAnteriorInicio = date('Y-m-d', strtotime('monday last week'));
$semanaAnteriorFin = date('Y-m-d', strtotime('sunday last week'));
$sqlAnterior = "SELECT COUNT(*) as total FROM reporte WHERE fecha_hora BETWEEN '$semanaAnteriorInicio' AND '$semanaAnteriorFin'";
$resultAnterior = $conn->query($sqlAnterior);
$rowAnterior = $resultAnterior->fetch_assoc();
$anterior = $rowAnterior['total'];

$actual = $stats['esta_semana'];
if ($anterior > 0) {
    $stats['aumento_semanal'] = round((($actual - $anterior) / $anterior) * 100);
} else {
    $stats['aumento_semanal'] = ($actual > 0) ? 100 : 0;
}

echo json_encode($stats);
$conn->close();
?>