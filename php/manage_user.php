<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gamedatabase";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['username']) && !empty(trim($_POST['username']))) {
    $username = trim($_POST['username']);

    // Cek apakah user sudah ada
    $stmt = $conn->prepare("SELECT player_id FROM player WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika user sudah ada, ambil player_id
        $row = $result->fetch_assoc();
        $_SESSION['player_id'] = $row['player_id'];
    } else {
        // Tambahkan user baru
        $stmt = $conn->prepare("INSERT INTO player (username) VALUES (?)");
        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan pengguna baru.']);
            exit;
        }
        $_SESSION['player_id'] = $stmt->insert_id;
    }

    $_SESSION['username'] = $username;

    // Mulai sesi game baru
    $culture_id = rand(1, 20);
    $time_left = 60;

    // Insert ke tabel edukasi
    $stmt = $conn->prepare("INSERT INTO edukasi (player_id, culture_id, time_left) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $_SESSION['player_id'], $culture_id, $time_left);

    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memulai sesi game.']);
        exit;
    }

    $_SESSION['session_id'] = $conn->insert_id;

    // Simpan skor awal 0
    $initial_score = 0;
    $stmt = $conn->prepare("INSERT INTO score (session_id, player_id, value_score) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $_SESSION['session_id'], $_SESSION['player_id'], $initial_score);
    
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan skor awal.']);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'player_id' => $_SESSION['player_id'],
        'username' => $_SESSION['username'],
        'session_id' => $_SESSION['session_id']
    ]);
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Username tidak diberikan.'
    ]);
}

$conn->close();
?>