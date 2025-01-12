<?php
require_once 'db_connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log incoming data
error_log("POST data received: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $score = intval($_POST['score']);
    $time_left = isset($_POST['time_left']) ? intval($_POST['time_left']) : 0;

    error_log("Processing data - Username: $username, Score: $score, Time Left: $time_left");

    try {
        // Updated query to include time_left
        $stmt = $conn->prepare("INSERT INTO scores (username, score, time_left) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sii", $username, $score, $time_left);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        error_log("Data successfully inserted into database");
        echo json_encode([
            "status" => "success", 
            "message" => "Skor berhasil disimpan.",
            "debug" => [
                "username" => $username,
                "score" => $score,
                "time_left" => $time_left
            ]
        ]);
    } catch (Exception $e) {
        error_log("Error in save_score.php: " . $e->getMessage());
        echo json_encode([
            "status" => "error", 
            "message" => "Gagal menyimpan skor: " . $e->getMessage(),
            "debug" => [
                "username" => $username,
                "score" => $score,
                "time_left" => $time_left
            ]
        ]);
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
} else {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>