// $(document).ready(function() {  
//     Fetch the final score from the server  
//     $.ajax({  
//         url: 'php/get_final_score.php',  
//         method: 'GET',  
//         success: function(data) {  
//             let result = JSON.parse(data);  
//             $('#finalScore').text("Username: " + result.username + " | Skor Akhir: " + result.score);  
//         },  
//         error: function() {  
//             $('#finalScore').text("Terjadi kesalahan saat mengambil skor.");  
//         }  
//     });  

//     // Restart game on button click  
//     $('#restartGame').click(function() {  
//         window.location.href = 'index.html'; // Redirect to the main game page  
//     });  
// });


// $(document).ready(function() {
//     $.ajax({
//         url: 'php/get_final_score.php', // Pastikan URL sesuai
//         method: 'GET',
//         success: function(response) {
//             if (response.status === 'success') {
//                 $('#username-display').text("Username: " + response.username);
//                 $('#score-display').text("Skor: " + response.score);
//             } else {
//                 alert(response.message);
//                 $('#username-display').text("Username: Pengguna");
//                 $('#score-display').text("Skor: 0");
//             }
//         },
//         error: function(xhr, status, error) {
//             alert("Terjadi kesalahan saat memuat data skor.");
//             console.error(xhr.responseText);
//         }
//     });


//     // Restart game on button click
//     $('#restartGame').click(function() {
//         window.location.href = 'index.html'; // Redirect to the main game page
//     });
// }



// );

$(document).ready(function() {
    $.ajax({
        url: 'php/get_final_score.php', // Hapus 'php/' jika file ada di folder yang sama
        method: 'GET',
        dataType: 'json', // Tambahkan ini untuk handling JSON otomatis
        success: function(response) {
            console.log('Response:', response); // Debugging
            if (response.status === 'success') {
                // Pastikan ID ini sesuai dengan yang ada di HTML
                $('#username').text(response.username);
                $('#final-score').text(response.score);
            } else {
                console.error('Error:', response.message);
                alert(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            alert('Terjadi kesalahan saat mengambil data skor');
        }
        
    });

    $('#mainLagi').click(function() {
        window.location.href = 'index.html';
    });
});