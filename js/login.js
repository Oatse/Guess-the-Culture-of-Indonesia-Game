$(document).ready(function() {  
    // Show the login screen by default until a username is provided  
    $('#login').show();  
    $('#game').hide();  

    // On clicking the start game button  
    $('#startGame').click(function() {  
        let username = $('#username').val().trim(); // Get the username input  

        // Basic validation for empty input  
        if (username.length === 0) {  
            alert("Silakan masukkan username.");  
            return ;  
        }  

        // // Redirect to game session page with username as a query parameter  
        // window.location.href = `game_session.html?username=${encodeURIComponent(username)}`; 

        // Reset session di server
        $.ajax({
            url: 'php/reset_session.php', // Endpoint untuk reset session
            method: 'POST',
            success: function() {
                console.log("Session direset.");
            },
            error: function() {
                console.error("Gagal mereset session.");
            }
        });

        // Save the username to the session and hide the login screen  
        $.ajax({  
            url: 'php/manage_user.php', // Endpoint to store the username  
            method: 'POST',  
            data: { username: username },  
            success: function(_response) {  
                // Optionally, handle response data (like current score if needed)  
                $('#login').hide(); // Hide login section  
                $('#game').show();  // Show game section  
                $('#displayUsername').text(username);
                loadNewCulture();   // Load the first culture for guessing  
            },  
            error: function() {  
                alert("Terjadi kesalahan. Coba lagi.");  
            }  
        });  
    });  
});