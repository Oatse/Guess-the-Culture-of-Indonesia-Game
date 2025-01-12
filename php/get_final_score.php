<?php
session_start();

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gamedatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Koneksi database gagal'
    ]));
}

if (isset($_SESSION['session_id']) && isset($_SESSION['player_id'])) {
    $session_id = $_SESSION['session_id'];
    $player_id = $_SESSION['player_id'];
    
    // Get both username and score
    $query = "SELECT p.username, COALESCE(SUM(s.value_score), 0) as total_score 
              FROM player p 
              LEFT JOIN score s ON p.player_id = s.player_id AND s.session_id = ? 
              WHERE p.player_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $session_id, $player_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode([
        'status' => 'success',
        'username' => $row['username'],
        'score' => $row['total_score']
    ]);
    
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data session tidak ditemukan',
        'username' => 'Pengguna',
        'score' => 0
    ]);
}

$conn->close();
?>