<?php

    require_once 'src/Wordle.php';
    require_once 'env.php';

    $env = new Environment();
    $ivLength = openssl_cipher_iv_length('AES-256-CBC');
    $iv = openssl_random_pseudo_bytes($ivLength);
    
    $keyHash = hash('sha256', $env->ENV['KEY'], true);

    if(!isset($_COOKIE['gamesPlayed'])) {
        setcookie('gamesPlayed', 0);
    }
    if(!isset($_COOKIE['gamesWon'])) {
        setcookie('gamesWon', 0);
    }
    if(!isset($_COOKIE['currentStreak'])) {
        setcookie('currentStreak', 0);
    }
    if(!isset($_COOKIE['attemptsPerGame'])) {
        setcookie('attemptsPerGame', 0);
    }

    if(!isset($_COOKIE['guess']) || empty($_COOKIE['guess'])) {
        $wordle = new Wordle();
        $ciphertext = openssl_encrypt($wordle->getWordToGuess(), 'AES-256-CBC', $keyHash, OPENSSL_RAW_DATA, $iv);
        $ciphertextBase64 = base64_encode($iv . $ciphertext);

        var_dump($ciphertextBase64, $wordle->getWordToGuess());

        setcookie('guess', $ciphertextBase64, time() + (86400 * 30), "/"); // 86400 = 1 day
    } else {
        $raw = base64_decode($_COOKIE['guess']);
        $iv = substr($raw, 0, $ivLength);
        $ciphertext = substr($raw, $ivLength);
        $plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $keyHash, OPENSSL_RAW_DATA, $iv);

        $wordle = new Wordle($plaintext, $_GET['attempts'] ?? 0);
        var_dump($_COOKIE['guess'], $wordle->getWordToGuess());
    }

?>
<html>
    <head>
        <title>Wordle</title>
        <link rel="stylesheet" type="text/css" href="styles.css">
        <script src="script.js"></script>
    </head>
    <body>
        <div class="container">
            <h1>Wordle</h1>
            <form id="guessForm" method="POST" action="index.php">
                <input type="text" name="guess" id="guess" maxlength="5" required>
                <button type="submit">Guess</button>
            </form>
            <div id="result"></div>
        </div>
        <script>
            document.getElementById('guessForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const guess = document.getElementById('guess').value;
                fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ guess })
                })
                .then(response => response.json())
                .then(data => {
                    const resultDiv = document.getElementById('result');
                    resultDiv.innerHTML = data.result.map(item => `<span class="${item.status}">${item.character}</span>`).join('');
                });
            });
        </script>
    </body>
</html>