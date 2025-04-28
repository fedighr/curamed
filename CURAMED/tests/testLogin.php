<?php
use PHPUnit\Framework\TestCase;

class testLogin extends TestCase
{
    private string $baseUrl          = 'http://localhost/curamed/CURAMED/';
    private string $testUserEmail    = 'test@curamed.com';
    private string $testUserPassword = 'ValidPassword123!';
    private string $cookieFile;

    protected function setUp(): void
    {
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'phpunit_cookies_');
        $this->createTestUser();
    }

    protected function tearDown(): void
    {
        @unlink($this->cookieFile);
    }

    public function testSQLInjectionProtection(): void
    {
        $data = $this->parseResponse($this->makeRequest('signin.php', [
            'email' => "' OR '1'='1' -- ",
            'mdp'   => 'anything'
        ]));
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Mot de passe incorrect', $data['message']);
    }

    public function testXSSInjectionProtection(): void
    {
        $response = $this->makeRequest('signin.php', [
            'email' => '<script>alert("hacked")</script>',
            'mdp'   => 'password123'
        ]);
        $this->assertStringNotContainsString('<script>', $response);
    }

    public function testInvalidLoginAttempt(): void
    {
        $data = $this->parseResponse($this->makeRequest('signin.php', [
            'email' => $this->testUserEmail,
            'mdp'   => 'wrong_password'
        ]));
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Mot de passe incorrect.', $data['message']);
        $this->assertStringContainsString('Tentatives restantes: 4', $data['message']);
    }

    public function testValidLogin(): void
    {
        $data = $this->parseResponse($this->makeRequest('signin.php', [
            'email' => $this->testUserEmail,
            'mdp'   => $this->testUserPassword
        ]));
        $this->assertTrue($data['success']);
    }

    public function testBruteForceProtection(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $data = $this->parseResponse($this->makeRequest('signin.php', [
                'email' => $this->testUserEmail,
                'mdp'   => 'bad' . $i
            ]));
            $this->assertStringContainsString(
                'Tentatives restantes: ' . (4 - $i),
                $data['message']
            );
        }

        $data = $this->parseResponse($this->makeRequest('signin.php', [
            'email' => $this->testUserEmail,
            'mdp'   => 'bad4'
        ]));
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Trop de tentatives', $data['message']);

        $data = $this->parseResponse($this->makeRequest('signin.php', [
            'email' => $this->testUserEmail,
            'mdp'   => $this->testUserPassword
        ]));
        $this->assertFalse($data['success']);
    }


    public function testDatabaseStress(): void
    {
        $iterations = 100;
        $successCount = 0;
        $startTime = microtime(true);


        $this->createMultipleTestUsers($iterations);


        for ($i = 0; $i < $iterations; $i++) {
            $cookieFile = tempnam(sys_get_temp_dir(), 'stress_cookie_');
            
            $response = $this->makeRequest('signin.php', [
                'email' => "testuser{$i}@curamed.com",
                'mdp'   => "Password{$i}!"
            ], $cookieFile);
            
            $data = $this->parseResponse($response);
            if ($data['success']) {
                $successCount++;
            }
            
            @unlink($cookieFile);
        }

        $totalTime = microtime(true) - $startTime;
        $avgResponseTime = $totalTime / $iterations;


        $this->assertEquals(
            $iterations,
            $successCount,
            "All $iterations login attempts should succeed"
        );
        
        $this->assertLessThan(
            0.5,
            $avgResponseTime,
            "Average response time should be under 0.5 seconds (actual: {$avgResponseTime}s)"
        );

        $conn = new mysqli(
            getenv('DB_HOST') ?: 'localhost',
            getenv('DB_USER') ?: 'root',
            getenv('DB_PASS') ?: '',
            getenv('DB_NAME') ?: 'curamed'
        );

        $conn->query("DELETE FROM utilisateur WHERE email LIKE '%@curamed.com'");

        $conn->close();

    }

    private function createMultipleTestUsers(int $count): void
    {
        $conn = new mysqli(
            getenv('DB_HOST') ?: 'localhost',
            getenv('DB_USER') ?: 'root',
            getenv('DB_PASS') ?: '',
            getenv('DB_NAME') ?: 'curamed'
        );

        for ($i = 0; $i < $count; $i++) {
            $email = "testuser{$i}@curamed.com";
            $password = password_hash("Password{$i}!", PASSWORD_DEFAULT);
            
            $conn->query(
                "INSERT INTO utilisateur 
                (nom, prenom, email, mot_de_passe, role, type_utilisateur)
                VALUES 
                ('User{$i}', 'Test', '$email', '$password', 'patient', 'regular')"
            );
        }

        $conn->close();
    }

    private function makeRequest(
        string $endpoint, 
        array $postData, 
        string $cookieFile = null
    ): string {
        $cookieFile = $cookieFile ?? $this->cookieFile;
        
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEJAR      => $cookieFile,
            CURLOPT_COOKIEFILE     => $cookieFile,
            CURLOPT_TIMEOUT        => 10,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $response !== false ? $response : '';
    }

    private function parseResponse(string $response): array
    {
        return json_decode($response, true) ?: [];
    }

    private function createTestUser(): void
    {
        $conn = new mysqli(
            getenv('DB_HOST') ?: 'localhost',
            getenv('DB_USER') ?: 'root',
            getenv('DB_PASS') ?: '',
            getenv('DB_NAME') ?: 'curamed'
        );

        $safeEmail = $conn->real_escape_string($this->testUserEmail);
        $conn->query("DELETE FROM utilisateur WHERE email = '$safeEmail'");

        $passwordHash = password_hash($this->testUserPassword, PASSWORD_DEFAULT);

        $conn->query(
            "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, type_utilisateur)
             VALUES ('Test', 'User', '$safeEmail', '$passwordHash', 'patient', 'normal')"
        );

        $conn->close();
    }
}
?>