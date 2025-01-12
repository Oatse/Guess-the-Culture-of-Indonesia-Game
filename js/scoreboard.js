$(document).ready(function() {
    // Ambil skor dan username dari server
    $.ajax({
        url: 'php/get_final_score.php', // Pastikan path file benar
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#username-display').text(response.username);
                $('#score-display').text(response.score);
            } else {
                alert("Gagal memuat data skor: " + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Kesalahan AJAX:", error);
            alert("Terjadi kesalahan saat memuat data papan skor.");
        }
    });
});
