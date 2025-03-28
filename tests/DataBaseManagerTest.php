<?php
use PHPUnit\Framework\TestCase;
use Mockery as m;

class DataBaseManagerTest extends TestCase {
    private $pdoMock;
    private $stmtMock;
    private $dbManager;

    protected function setUp(): void {
        // Création d'un mock de PDO
        $this->pdoMock = m::mock(PDO::class);

        // Création d'un mock de PDOStatement
        $this->stmtMock = m::mock(PDOStatement::class);
        $this->stmtMock->shouldReceive('execute')->andReturn(true);
        $this->stmtMock->shouldReceive('fetchAll')->andReturn([]);

        // Simulation des méthodes de PDO
        $this->pdoMock->shouldReceive('prepare')->andReturn($this->stmtMock);

        // Injection du mock dans DataBaseManager
        $this->dbManager = new DataBaseManager($this->pdoMock);
    }

    public function testExecuteDataBase(): void {
        $query = "SELECT * FROM users WHERE id = :id";
        $params = [':id' => 1];

        $stmt = $this->dbManager->executeDataBase($query, $params);
        $this->assertInstanceOf(PDOStatement::class, $stmt);
    }

    protected function tearDown(): void {
        m::close();
    }
}