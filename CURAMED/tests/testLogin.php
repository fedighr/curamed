<?php

use PHPUnit\Framework\TestCase;

class testLogin extends TestCase
{
    protected $baseUrl = 'http://localhost/curamed/CURAMED/';
    protected $testUserEmail = 'test@curamed.com';
    protected $testUserPassword = 'ValidPassword123!';

    protected function setUp(): void
    {

        $this->createTestUser();
    }

    protected function tearDown(): void
    {

        $this->deleteTestUser();
    }


    public function testSQLInjectionProtection()
    {
        $response = $this->makeRequest('signin.php', [
            'email' => "' OR '1'='1' -- ",
            'mdp' => "anything"
        ]);

        $data = $this->parseResponse($response);
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Aucun utilisateur', $data['message']);
    }


    public function testXSSInjectionProtection()
    {
        $response = $this->makeRequest('signin.php', [
            'email' => '<script>alert("hacked")</script>',
            'mdp' => 'password123'
        ]);

        $this->assertStringNotContainsString('<script>', $response);
    }


    public function testInvalidLoginAttempt()
    {
        $response = $this->makeRequest('signin.php', [
            'email' => $this->testUserEmail,
            'mdp' => 'wrong_password'
        ]);

        $data = $this->parseResponse($response);
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Mot de passe incorrect.', $data['message']);
        $this->assertStringContainsString('Tentatives restantes: 4', $data['message']);
    }


    public function testValidLogin()
    {
        $response = $this->makeRequest('signin.php', [
            'email' => $this->testUserEmail,
            'mdp' => $this->testUserPassword
        ]);

        $data = $this->parseResponse($response);
        $this->assertTrue($data['success']);
    }

    public function testBruteForceProtection()
    {
        for ($i = 0; $i < 4; $i++) {
            $response = $this->makeRequest('signin.php', [
                'email' => $this->testUserEmail,
                'mdp' => 'wrong_password_'.$i
            ]);
            
            $data = $this->parseResponse($response);
            $remaining = 4 - $i;
            $this->assertStringContainsString("Tentatives restantes: $remaining", $data['message']);
        }


        $response = $this->makeRequest('signin.php', [
            'email' => $this->testUserEmail,
            'mdp' => 'wrong_password_4'
        ]);
        
        $data = $this->parseResponse($response);
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Trop de tentatives', $data['message']);


        $response = $this->makeRequest('signin.php', [
            'email' => $this->testUserEmail,
            'mdp' => $this->testUserPassword
        ]);
        $this->assertFalse($this->parseResponse($response)['success']);
    }

    private function makeRequest($endpoint, $postData)
    {
        $url = $this->baseUrl . $endpoint;
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($postData),
            ],
        ];
        
        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    private function parseResponse($response)
    {
        return json_decode($response, true);
    }

    private function createTestUser()
    {
        $conn = new mysqli(
            getenv('DB_HOST') ?: 'localhost',
            getenv('DB_USER') ?: 'root',
            getenv('DB_PASS') ?: '',
            getenv('DB_NAME') ?: 'curamed'
        );

        $passwordHash = password_hash($this->testUserPassword, PASSWORD_DEFAULT);
        

        $conn->query("DELETE FROM utilisateur WHERE email = '".$this->testUserEmail."'");
        

        $conn->query("INSERT INTO utilisateur (email, mot_de_passe) VALUES (
            '".$this->testUserEmail."',
            '".$passwordHash."'
        )");
        
        $conn->close();
    }

    private function deleteTestUser()
    {
        $conn = new mysqli(
            getenv('DB_HOST') ?: 'localhost',
            getenv('DB_USER') ?: 'root',
            getenv('DB_PASS') ?: '',
            getenv('DB_NAME') ?: 'curamed'
        );

        $conn->query("DELETE FROM utilisateur WHERE email = '".$this->testUserEmail."'");
        $conn->close();
    }
}