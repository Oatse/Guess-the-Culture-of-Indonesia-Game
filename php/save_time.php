<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gamedatabase";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => "Connection failed: " . $conn->connect_error]));
}

// Check if we have the required session data
if (!isset($_SESSION['session_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'No active session found']));
}

// Get the time_left from POST data
if (!isset($_POST['time_left'])) {
    die(json_encode(['status' => 'error', 'message' => 'No time data provided']));
}

$session_id = $_SESSION['session_id'];
$time_left = (int)$_POST['time_left'];

// Update the time_left in the edukasi table
$stmt = $conn->prepare("UPDATE edukasi SET time_left = ? WHERE session_id = ?");
$stmt->bind_param("ii", $time_left, $session_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Time updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update time: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>