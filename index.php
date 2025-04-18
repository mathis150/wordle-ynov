<?php

    require_once 'src/Wordle.php';
    require_once 'env.php';

    $gameover = false;
    $victory = false;
    $env = new Environment();
    $ivLength = openssl_cipher_iv_length('AES-256-CBC');
    $iv = openssl_random_pseudo_bytes($ivLength);
    
    $keyHash = hash('sha256', $env->ENV['KEY'], true);

    if(!isset($_GET['attempts'])){ $_GET['attempts'] = 1; }

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

        setcookie('guess', $ciphertextBase64, time() + (86400 * 30), "/");
    } else {
        $raw = base64_decode($_COOKIE['guess']);
        $iv = substr($raw, 0, $ivLength);
        $ciphertext = substr($raw, $ivLength);
        $plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $keyHash, OPENSSL_RAW_DATA, $iv);

        $wordle = new Wordle($plaintext, $_GET['attempts'] ?? 0);
    }

    if(isset($_POST['submit'])) {
        $guess = $_POST['guess'] ?? null;
        if($guess) {
            try {
                $result = $wordle->guessTheWord($guess);
                $status = $result['status'];
                $letters = $result['letters'];
                $remainingAttempts = $result['remaining_attempts'];
                if($status == "correct") { $victory = true; }
            } catch (Exception $e) {
                $gameover = true;
            }
        } else {
            echo "Please enter a valid guess.";
        }
    } else {
        $result = null;
    }

    if(isset($_POST['restart'])) {
        setcookie('guess', '', time() - 3600, "/");
        setcookie('gamesPlayed', $_COOKIE['gamesPlayed'] + 1);
        setcookie('currentStreak', 0);

        $attempsPerGames = json_decode($_COOKIE['attemptsPerGame'], true);
        if (!is_array($attempsPerGames)) {
            $attempsPerGames = [];
        }
        $attempsPerGames[] = 6;

        setcookie('attemptsPerGame', json_encode($attempsPerGames));

        header('Location: index.php');
    }

    if(isset($_POST['victory'])) {
        setcookie('guess', '', time() - 3600, "/");
        setcookie('gamesPlayed', $_COOKIE['gamesPlayed'] + 1);
        setcookie('currentStreak', $_COOKIE['currentStreak'] + 1);
        setcookie('gamesWon', $_COOKIE['gamesWon'] + 1);

        $attempsPerGames = json_decode($_COOKIE['attemptsPerGame'], true);
        if (!is_array($attempsPerGames)) {
            $attempsPerGames = [];
        }
        $attempsPerGames[] = $_GET['attempts'];

        setcookie('attemptsPerGame', json_encode($attempsPerGames));

        header('Location: index.php');
    }
?>
<html>
    <head>
        <title>Wordle</title>
        <link rel="stylesheet" type="text/css" href="styles.css">
        <script src="script.js"></script>
    </head>
    <body>
        <?php
        if(!$gameover && !$victory)
        {
        ?>
            <div class="container">
                <h1>Wordle</h1>
                <hr>
                <h3>Statistics :</h3>
                <p>- Games played : <?php echo $_COOKIE['gamesPlayed']?></p>
                <p>- Games won : <?php echo $_COOKIE['gamesWon']?></p>
                <p>- Win streak : <?php echo $_COOKIE['currentStreak']?></p>
                <p>- Attemps :</p>
                <div id="statistics"></div>
                <hr>
                <form method="POST" action="index.php?attempts=<?php echo ((int)$_GET['attempts']) + 1; ?>">
                    <input type="text" name="guess" id="guess" maxlength="5" required>
                    <button name ="submit" type="submit">Guess</button>
                </form>
                <div id="result" style="display: flex;">
                    <?php
                    if($result) {
                        foreach($letters as $letter) {
                            if ($letter['status'] === 'correct') {
                                echo "<div style=\"width: 30px; height: 30px; border: 1px solid #000; background: green; display: flex; justify-content: center; align-items: center;\">".$letter['character']."</div>";
                            } elseif ($letter['status'] === 'partially correct') {
                                echo "<div style=\"width: 30px; height: 30px; border: 1px solid #000; background: orange; display: flex; justify-content: center; align-items: center;\">".$letter['character']."</div>";
                            } else {
                                echo "<div style=\"width: 30px; height: 30px; border: 1px solid #000; background: red; display: flex; justify-content: center; align-items: center;\">".$letter['character']."</div>";
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        <?php
        } elseif($victory) {
        ?>
            <div class='game-over'>Victory !</div>
            <form method="POST" action="index.php">
                <input type="submit" name="victory" value="Relancer une partie">
            </form>
        <?php
        } else {
        ?>
            <div class='game-over'>Game Over! The word was: <?php echo htmlspecialchars($wordle->getWordToGuess()); ?></div>
            <form method="POST" action="index.php">
                <input type="submit" name="restart" value="Ressayer">
            </form>
        <?php
        }
        ?>
    </body>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current',{packages:['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            const data = new google.visualization.DataTable();
            data.addColumn('number', 'Game ID');
            data.addColumn('number', 'Attemps');

            
                        
            data.addRows([<?php
            $attempsPerGames = json_decode($_COOKIE['attemptsPerGame'], true);
            if (!is_array($attempsPerGames)) {
                $attempsPerGames = [];
            }

            foreach($attempsPerGames as $key => $value) {
                echo "[".$key.", ".$value."],";
            }
            ?>]);

            const options = {
                title: 'lux',
                hAxis: {title: 'Date'},
                vAxis: {title: 'Value'},
                legend: 'none'
            };

            const chart = new google.visualization.LineChart(document.getElementById('statistics'));
            chart.draw(data, options);
        }
    </script>
</html>