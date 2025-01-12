<?php
session_start();
session_destroy(); // Hapus semua data session
session_start(); // Mulai session baru
echo json_encode(["status" => "success", "message" => "Session direset."]);
?>
