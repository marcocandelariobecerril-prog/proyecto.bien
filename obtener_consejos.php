<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "guardian_aire_db");
if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

// Crear tabla consejos si no existe
$conn->query("CREATE TABLE IF NOT EXISTS consejos (
    id_consejo INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen_url VARCHAR(255)
)");

// Insertar consejos por defecto si no hay datos
$result = $conn->query("SELECT COUNT(*) as total FROM consejos");
$row = $result->fetch_assoc();
if ($row['total'] == 0) {
    $conn->query("INSERT INTO consejos (titulo, descripcion) VALUES
        ('No quemes basura', 'Quemar basura contamina el aire que respiramos y daña la salud de todos. Lleva tus residuos al camión de basura.'),
        ('Planta un árbol', 'Los árboles limpian el aire, dan sombra y embellecen nuestra comunidad. ¡Planta uno en tu casa!'),
        ('Separa tu basura', 'Orgánico, plástico, vidrio y papel. Separar ayuda a reciclar y reduce la contaminación.'),
        ('Cuida el agua', 'No tires basura en ríos y barrancas. El agua es vida, cuidémosla.'),
        ('Reporta problemas', 'Usa Guardián del Aire para reportar quemas, tala y basura. ¡Tú puedes hacer la diferencia!')
    ");
}

$sql = "SELECT * FROM consejos ORDER BY id_consejo";
$result = $conn->query($sql);
$consejos = [];
while ($row = $result->fetch_assoc()) {
    $consejos[] = $row;
}

echo json_encode($consejos);
$conn->close();
?>