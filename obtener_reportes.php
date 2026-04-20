<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "guardian_aire_db");
if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

$sql = "SELECT r.id_reporte, r.tipo_id, t.nombre AS tipo_nombre, r.descripcion, r.latitud, r.longitud, r.foto_url, r.fecha_hora 
        FROM reporte r
        JOIN tipo_problema t ON r.tipo_id = t.id_tipo
        ORDER BY r.id_reporte DESC";
$result = $conn->query($sql);

$reportes = [];
while ($row = $result->fetch_assoc()) {
    $reportes[] = $row;
}

echo json_encode($reportes);
$conn->close();
?>