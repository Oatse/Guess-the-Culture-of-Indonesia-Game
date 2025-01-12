<?php  
$servername = "localhost";  
$username = "root";  
$password = "";  
$dbname = "gamedatabase";  

$conn = new mysqli($servername, $username, $password, $dbname);  
if ($conn->connect_error) {  
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);  
    exit;  
}  

$sql = "SELECT culture_id, c_name, clue_1, clue_2, clue_3 FROM c_indonesia ORDER BY RAND() LIMIT 1";  
$result = $conn->query($sql);  

if ($result === FALSE) {  
    echo json_encode(['status' => 'error', 'message' => "Query error: " . $conn->error]);  
    exit;  
}  

if ($result->num_rows > 0) {  
    $row = $result->fetch_assoc();  
    echo json_encode(['status' => 'success', 'data' => $row]);  
} else {  
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada data budaya ditemukan.']);  
}  

$conn->close();  
?>