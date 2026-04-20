<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "guardian_aire_db";

// Crear directorio de uploads si no existe
$uploadDir = __DIR__ . '/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

// Recibir datos
$tipo_id = $_POST['tipo_id'] ?? null;
$descripcion = $_POST['descripcion'] ?? '';
$latitud = $_POST['latitud'] ?? null;
$longitud = $_POST['longitud'] ?? null;
$fecha_hora_foto = $_POST['fecha_hora_foto'] ?? date('Y-m-d H:i:s');

// Validar datos requeridos
if (!$tipo_id || !$latitud || !$longitud) {
    echo json_encode(["error" => "Faltan datos requeridos"]);
    exit;
}

// Procesar la foto
$foto_url = '';
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['foto'];
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
    $rutaDestino = $uploadDir . $nombreUnico;
    
    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        $foto_url = $nombreUnico;
    } else {
        echo json_encode(["error" => "Error al guardar la foto"]);
        exit;
    }
} else {
    echo json_encode(["error" => "No se recibió ninguna foto"]);
    exit;
}

// Insertar en la base de datos
$sql = "INSERT INTO reporte (tipo_id, descripcion, latitud, longitud, foto_url, fecha_hora) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issdss", $tipo_id, $descripcion, $latitud, $longitud, $foto_url, $fecha_hora_foto);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true, 
        "message" => "Reporte guardado con éxito",
        "id" => $conn->insert_id
    ]);
} else {
    echo json_encode(["error" => "Error al guardar: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>