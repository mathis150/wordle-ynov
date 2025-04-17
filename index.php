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