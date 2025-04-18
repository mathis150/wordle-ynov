<?php
declare(strict_types=1);

class Wordle {
    private string $wordToGuess;
    private int $attempts;
    private int $maxAttempts;
    private array $letters;
    private array $dictionary;

    // Statistiques
    private int $gamesPlayed;
    private int $gamesWon;
    private int $currentStreak;
    private array $attemptsPerGame;

    public function __construct(?string $guess = null, ?int $attempts = null) {
        $this->dictionary = [
            "apple", "brave", "chair", "dream", "eagle", "flame", "grape", "house", "index",
            "joker", "knife", "lemon", "mango", "night", "ocean", "piano", "queen", "river",
            "stone", "table", "union", "vivid", "whale", "xenon", "young", "zebra"
        ];

        $this->wordToGuess = $guess ?? $this->generateGuess();
        $this->attempts = $attempts ?? 0;
        $this->maxAttempts = 6;
        $this->letters = [];

        $this->gamesPlayed = (int) $_COOKIE['gamesPlayed'] ?? 0;
        $this->gamesWon = (int) $_COOKIE['gamesWon'] ?? 0;
        $this->currentStreak = (int) $_COOKIE['currentStreak'] ?? 0;
        $this->attemptsPerGame = (array) $_COOKIE['attemptsPerGame'] ?? [];
    }

    public function guessTheWord(string $word): array {
        $this->gamesPlayed++;
        if (!preg_match('/^[a-zA-Z]{5}$/', $word)) {
            throw new InvalidArgumentException('Guess must be exactly 5 alphabetic characters.');
        }

        if ($this->attempts >= $this->maxAttempts) {
            throw new RuntimeException('Game over: no attempts left.');
        }

        $target = str_split($this->wordToGuess);
        $guess = str_split($word);
        $result = [];
        $status = 'correct';

        $used = array_fill(0, 5, false);
        $correct_elements = 0;

        // 1st pass: exact matches
        for ($i = 0; $i < 5; $i++) {
            if ($guess[$i] === $target[$i]) {
                $result[$i] = ['character' => $guess[$i], 'status' => 'correct'];
                $used[$i] = true;
                $correct_elements++;
            } else {
                $result[$i] = null;
            }
        }

        // 2nd pass: partial matches
        for ($i = 0; $i < 5; $i++) {
            if ($result[$i] === null) {
                $found = false;
                for ($j = 0; $j < 5; $j++) {
                    if (!$used[$j] && $guess[$i] === $target[$j]) {
                        $used[$j] = true;
                        $result[$i] = ['character' => $guess[$i], 'status' => 'partially correct'];
                        $status = 'partially correct';
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $result[$i] = ['character' => $guess[$i], 'status' => 'incorrect'];
                    $status = ($status === 'correct') ? 'incorrect' : $status;
                    if($correct_elements !== 0) {
                        $status = 'partially correct';
                    }
                }
            }
        }

        $this->letters[$this->attempts] = $result;
        $this->attempts++;

        if ($word === $this->wordToGuess) {
            $this->gamesWon++;
            $this->currentStreak++;
            $this->attemptsPerGame[] = $this->attempts;
        }

        if ($this->isGameOver()) {
            if ($word !== $this->wordToGuess) {
                $this->currentStreak = 0;
                $this->attemptsPerGame[] = $this->maxAttempts;
            }
        }

        return [
            'status' => $status,
            'letters' => $result,
            'remaining_attempts' => $this->maxAttempts - $this->attempts
        ];
    }

    public function generateGuess(): string {
        return $this->dictionary[array_rand($this->dictionary)];
    }

    public function isGameOver(): bool {
        return $this->attempts >= $this->maxAttempts;
    }

    public function getStats(): array {
        $averageAttempts = empty($this->attemptsPerGame) ? 0 :
            array_sum($this->attemptsPerGame) / count($this->attemptsPerGame);

        return [
            'gamesPlayed' => $this->gamesPlayed,
            'gamesWon' => $this->gamesWon,
            'currentStreak' => $this->currentStreak,
            'averageAttempts' => round($averageAttempts, 2)
        ];
    }

    public function getWordToGuess(): string {
        return $this->wordToGuess;
    }
}
