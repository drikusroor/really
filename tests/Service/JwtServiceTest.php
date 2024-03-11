<?php

namespace Ainab\Really\Service;

use PHPUnit\Framework\TestCase;

class JwtServiceTest extends TestCase
{
    private $jwtHandler;
    private $secretKey;

    protected function setUp(): void
    {
        // Set the JWT_SECRET environment variable
        $this->secretKey = '1234567890';
        $_ENV['JWT_SECRET'] = $this->secretKey;

        $this->jwtHandler = new JwtService();
    }

    public function testGenerateToken()
    {
        $payload = ['user_id' => 123];
        $token = $this->jwtHandler->generateToken($payload);

        // Assert that the token is not empty
        $this->assertNotEmpty($token);

        // Assert that the token is a string
        $this->assertIsString($token);

        // Assert that the token contains the base64UrlHeader
        $this->assertStringContainsString($this->jwtHandler->base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256'])), $token);

        // Assert that the token contains the base64UrlPayload
        $this->assertStringContainsString($this->jwtHandler->base64UrlEncode(json_encode($payload)), $token);

        // Assert that the token contains the base64UrlSignature
        $this->assertStringContainsString(
            $this->jwtHandler->base64UrlEncode(
                hash_hmac(
                    'sha256',
                    $this->jwtHandler->base64UrlEncode(
                        json_encode(['typ' => 'JWT', 'alg' => 'HS256'])
                    ) . "." . $this->jwtHandler->base64UrlEncode(json_encode($payload)),
                    '1234567890',
                    true
                )
            ),
            $token
        );
    }

    public function testParseToken()
    {
        // Create a mock JWT token
        $payload = ['user_id' => 123];
        $token = $this->jwtHandler->generateToken($payload);

        // Set the mock JWT token as a cookie
        $_COOKIE['jwt'] = $token;

        // Call the parseToken method
        $parsedToken = $this->jwtHandler->parseToken($token);

        // Assert that the parsed token is not null
        $this->assertNotNull($parsedToken);

        // Assert that the parsed token is an array
        $this->assertIsArray($parsedToken);

        // Assert that the parsed token contains the correct payload
        $this->assertEquals($payload, $parsedToken);
    }
}
