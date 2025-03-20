<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = ""; 
$basedades = "productes";

$conn = new mysqli($servername, $username, $password, $basedades);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Connexió fallida: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['id'])) {
    $sql = "SELECT * FROM productes";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $productes = [];
        while ($row = $result->fetch_assoc()) {
            $productes[] = $row;
        }
        echo json_encode($productes);
    } else {
        echo json_encode([]);
    }
}

else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM productes WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $producte = $result->fetch_assoc();
        echo json_encode($producte);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Producte no trobat"]);
    }
}

else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['nom']) && isset($data['preu'])) {
        $nom = $conn->real_escape_string($data['nom']);
        $preu = floatval($data['preu']);

        $sql = "INSERT INTO productes (nom, preu) VALUES ('$nom', $preu)";
        if ($conn->query($sql) == TRUE) {
            http_response_code(201);
            echo json_encode(["missatge" => "Producte afegit correctament"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error afegint producte: " . $conn->error]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Dades invàlides"]);
    }
}

$conn->close();
?>