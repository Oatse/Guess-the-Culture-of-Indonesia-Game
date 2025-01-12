<?php
session_start();

header('Content-Type: application/json');

// Validasi apakah sesi `username` dan `current_score` tersedia
if (isset($_SESSION['username']) && isset($_SESSION['current_score'])) {
    echo json_encode([
        'status' => 'success',
        'username' => $_SESSION['username'],
        'score' => $_SESSION['current_score']
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data username atau skor tidak ditemukan.',
        'username' => 'Pengguna',
        'score' => 0
    ]);
}
print_r($_SESSION);
?>
