<?php
use PHPUnit\Framework\TestCase;
use Mockery as m;

class WordleTest extends TestCase {
    private $wordle;

    protected function setUp(): void {
        $this->wordle = new Wordle('apple');
    }

    public function testCorrectGuess(): void {
        $result = $this->wordle->guessTheWord('apple');
        $this->assertEquals('correct', $result['status']);
    }

    public function testPartiallyCorrectGuess(): void {
        $result = $this->wordle->guessTheWord('appla');
        $this->assertEquals('partially correct', $result['status']);
    }

    public function testIncorrectGuess(): void {
        $result = $this->wordle->guessTheWord('xxxxx'); // mot non dans le dictionnaire
        $this->assertEquals('incorrect', $result['status']);
    }

    public function testGeneratedWordIsString(): void {
        $word = $this->wordle->generateGuess();
        $this->assertIsString($word);
        $this->assertEquals(5, strlen($word));
    }

    public function testInvalidWordTooShort(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->wordle->guessTheWord('app');
    }

    public function testInvalidWordWithNumbers(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->wordle->guessTheWord('appl3');
    }

    public function testGameOverAfterTooManyAttempts(): void {
        for ($i = 0; $i < 6; $i++) {
            try {
                $this->wordle->guessTheWord('grape');
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $this->expectException(RuntimeException::class);
        $this->wordle->guessTheWord('grape');
    }

    public function testGameStatisticsAfterWinWinStat(): void {
        $this->wordle->guessTheWord('apple');
        $stats = $this->wordle->getStats();
        $this->assertEquals(1, $stats['gamesWon']);
    }
    public function testGameStatisticsAfterWinPlayedStat(): void {
        $this->wordle->guessTheWord('apple');
        $stats = $this->wordle->getStats();
        $this->assertEquals(1, $stats['gamesPlayed']);
    }
    public function testGameStatisticsAfterWinStreakStat(): void {
        $this->wordle->guessTheWord('apple');
        $stats = $this->wordle->getStats();
        $this->assertEquals(1, $stats['currentStreak']);
    }
    public function testGameStatisticsAfterWinAttemptStat(): void {
        $this->wordle->guessTheWord('apple');
        $stats = $this->wordle->getStats();
        $this->assertEquals(1, $stats['averageAttempts']);
    }

    protected function tearDown(): void {
        m::close();
    }
}
