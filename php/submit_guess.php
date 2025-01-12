<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gamedatabase";

// Buat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validasi input
if (!isset($_POST['guess']) || !isset($_POST['current_c_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tebakan atau ID budaya tidak diberikan.']);
    exit;
}

$guess = trim($_POST['guess']);
$current_c_id = (int)$_POST['current_c_id'];

// Memastikan session_id ada
if (!isset($_SESSION['session_id']) || !isset($_SESSION['player_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session ID atau Player ID tidak tersedia.']);
    exit;
}

$session_id = $_SESSION['session_id'];
$player_id = $_SESSION['player_id'];

// Fetch the correct answer dari database
$stmt = $conn->prepare("SELECT c_name FROM c_indonesia WHERE culture_id = ?");
$stmt->bind_param("i", $current_c_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $correct_answer = $row['c_name'];
    
    // Check if the guess is correct
    if (strcasecmp($guess, $correct_answer) == 0) {
        // Tebakan benar, tambah 10 ke skor yang sudah ada
        $stmt = $conn->prepare("UPDATE score SET value_score = value_score + 10 WHERE session_id = ? AND player_id = ?");
        $stmt->bind_param("ii", $session_id, $player_id);

        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => "Error executing query: " . $stmt->error]);
            exit;
        }

        // Update session score
        $_SESSION['score'] = ($_SESSION['score'] ?? 0) + 10;
        $_SESSION['round_count'] = ($_SESSION['round_count'] ?? 0) + 1;
        
        // Periksa apakah ini ronde terakhir
        if (isset($_SESSION['round_count']) && $_SESSION['round_count'] >= 5) {
            // Simpan skor akhir ke session
            $_SESSION['current_score'] = $_SESSION['score'];
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Game Selesai! Total Skor: ' . $_SESSION['score'],
                'redirect' => 'final_score.html'
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Tebakan Anda Benar!',
                'score' => $_SESSION['score']
            ]);
        }
    } else {
        // Tebakan salah
         echo json_encode(['status' => 'error', 'message' => 'Tebakan Anda Salah. Jawaban yang benar adalah: ' . $correct_answer]);
         $_SESSION['username'] = $username; // Pastikan username tersimpan
         $_SESSION['current_score'] = $_SESSION['score'] ?? 0; // Simpan skor akhir
         header("final_score.html"); // Arahkan ke papan skor jika salah
         exit();
 
         if (isset($_SESSION['round_count']) && $_SESSION['round_count'] >= 5) {
             // Update skor final ke session
             $_SESSION['current_score'] = $_SESSION['score'];
             echo json_encode([
                 'status' => 'success', 
                 'message' => 'Game Selesai! Total Skor: ' . $_SESSION['score'],
                 'score' => $_SESSION['score'],
                 'redirect' => 'final_score.html'
             ]);
             exit();
         }
     }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada data untuk culture_id yang diberikan.']);
}

// Tutup koneksi
$stmt->close();
$conn->close();
?>