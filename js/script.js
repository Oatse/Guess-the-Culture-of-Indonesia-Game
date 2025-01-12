let currentCultureId;
let currentScore = 0;
let roundCount = 0;
let totalRounds = 5;
let username = '';
let timeLeft = 60;
let timerId = null;



// Timer Functions
function startTimer() {
    if (timerId) {
        clearInterval(timerId);
    }
    
    timeLeft = 60;
    updateTimerDisplay();
    
    timerId = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        
        if (timeLeft <= 0) {
            clearInterval(timerId);
            gameOver();
        } else if (timeLeft <= 10) {
            $('.timer-circle').addClass('warning');
        } else {
            $('.timer-circle').removeClass('warning');
        }
    }, 1000);
}

function updateTimerDisplay() {
    $('#timer').text(timeLeft);
}

function addTime() {
    timeLeft += 60;
    $('.timer-circle').removeClass('warning');
    updateTimerDisplay();
}

function gameOver() {
    clearInterval(timerId);
    showWrongAnswerPopup("Waktu Habis!", currentScore);
}

// Popup Functions
function showWrongAnswerPopup(correctAnswer, score) {
    $('#correctAnswer').text(correctAnswer);
    $('#finalScorePopup').text(score);
    $('.popup-overlay').fadeIn(300);
    $('.clue-buttons').fadeOut(300);

    // saveGameResults();
    
    setTimeout(function() {
        window.location.href = 'final_score.html';
    }, 3000);
}

$(document).ready(function() {
    // Hide clue buttons initially
    $('.clue-buttons').hide();
    
    // Start Game Handler
    $('#startGame').click(function() {
        username = $('#username').val().trim();
        if (username.length === 0) {
            alert("Silakan masukkan username.");
            return;
        }

        $.ajax({
            url: 'php/manage_user.php',
            method: 'POST',
            data: { username: username },
            success: function(response) {
                let result = JSON.parse(response);
                if (result.status === 'success') {
                    // Hide login screen and show game
                    $('#login').hide();
                    $('#game').show();
                    $('.clue-buttons').css('display', 'flex').hide().fadeIn(800);
                    $('#displayUsername').text(username);
                    
                    // Start game and timer
                    loadNewCulture(result.player_id);
                    startTimer();
                }
            },
            error: function() {
                alert("Terjadi kesalahan. Coba lagi.");
            }
        });
    });

    // Load New Culture
    function loadNewCulture(player_id) {
        
        
        $.ajax({
            url: 'php/get_culture.php',
            method: 'GET',
            data: { player_id: player_id },
            success: function(response) {
                let result = JSON.parse(response);
                
                if (result.status === 'error') {
                    $('#error-message').text(result.message).show();
                    return;
                }
                
                let cultureData = result.data;
                currentCultureId = cultureData.culture_id;
                $('#c_name').text(cultureData.c_name);
                $('#clue_1').text(cultureData.clue_1);
                $('#clue_2').text(cultureData.clue_2).hide();
                $('#clue_3').text(cultureData.clue_3).hide();
                $('#culture-image').attr('src', cultureData.clue_1).show();
                $('#score').text("Skor: " + currentScore);
                $('#error-message').hide();
            },
            error: function() {
                $('#error-message').text("Terjadi kesalahan saat memuat data budaya.").show();
            }
        });
    }

    // Clue Button Handlers
    $('#showClue2').click(function() {
        $('#clue_2').show();
    });

    $('#showClue3').click(function() {
        $('#clue_3').show();
    });

    // Submit Guess Handler
    $('#submitGuess').click(function() {
        let guess = $('#guess').val();
        if (guess.trim().length === 0) {
            alert("Silakan masukkan tebakan!");
            return;
        }

        $.ajax({
            url: 'php/submit_guess.php',
            method: 'POST',
            data: { guess: guess, current_c_id: currentCultureId, timeLeft: timeLeft },
            success: function(response) {
                let result = JSON.parse(response);
                $('#guess').val('');

                if (result.status === 'success') {
                    currentScore += 10;
                    roundCount++;
                    $('#score').text("Skor: " + currentScore);
                    addTime(); // Add 60 seconds for correct answer

                    if (roundCount < totalRounds) {
                        loadNewCulture(currentCultureId);
                    } else {
                        clearInterval(timerId);
                        $('#result').append("<br>Game Selesai! Total Skor: " + currentScore);
                        saveGameResults();
                    }
                } else {
                    clearInterval(timerId);
                    showWrongAnswerPopup(result.message.split(': ')[1], currentScore);
                }
            },
            error: function() {
                alert("Terjadi kesalahan saat mengirim tebakan.");
            }
        });
    });

    // Save Game Results
    function saveGameResults() {
        // Save score
        $.ajax({
            url: 'php/save_score.php',
            method: 'POST',
            data: {
                session_id: sessionId,
                player_id: playerId,
                value_score: currentScore
            },
            success: function(response) {
                console.log("Skor berhasil disimpan.");
                setTimeout(function() {
                    window.location.href = 'final_score.html';
                }, 2000);
            },
            error: function() {
                console.error("Gagal menyimpan skor.");
            }
        });
    }
    

    // End Game Check
    if (roundCount >= totalRounds) {
        clearInterval(timerId);
        $('#result').append("<br>Game Selesai! Total Skor: " + currentScore);
        saveGameResults();
    }
});