<?php  
require_once 'db_connection.php'; // Koneksi ke database  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
    $username = $_POST['username']; // Ambil username dari POST  
    $score = intval($_POST['score']); // Ambil dan pastikan skor adalah integer  

    // Query untuk menyimpan username dan skor ke database  
    $stmt = $conn->prepare("INSERT INTO score (username, score) VALUES (?, ?)");  
    $stmt->bind_param("si", $username, $score); // 's' untuk string, 'i' untuk integer  

    if ($stmt->execute()) {  
        echo json_encode(["status" => "success", "message" => "Username dan skor berhasil disimpan."]);  
    } else {  
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan username dan skor."]);  
    }  

    $stmt->close(); // Tutup statement  
    $conn->close(); // Tutup koneksi database  
}  
?>