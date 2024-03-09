<?php

namespace Ainab\Really\Service;

class JwtService {
    private $secretKey;
    private $algorithm;

    public function __construct($key, $algo = 'HS256') {
        $this->secretKey = $key;
        $this->algorithm = $algo;
    }

    // Base64Url encode
    public function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // Base64Url decode
    public function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }

    // Generate a JWT token
    public function generateToken($payload) {
        if (isset($payload['password'])) {
            unset($payload['password']);
        }

        $header = json_encode(['typ' => 'JWT', 'alg' => $this->algorithm]);
        $payload = json_encode($payload);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = hash_hmac($this->getAlgorithm(), $base64UrlHeader . "." . $base64UrlPayload, $this->secretKey, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        
        setcookie('jwt', $jwt, time() + 3600, "/"); // 1-hour expiration, adjust as needed
        return $jwt;
    }

    // Parse and validate a JWT token from cookies
    public function parseToken() {
        if (!isset($_COOKIE['jwt'])) {
            return null; // No JWT cookie present
        }

        $jwt = $_COOKIE['jwt'];
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];

        // Build a signature based on the header and payload using the secret
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);
        $signature = hash_hmac($this->getAlgorithm(), $base64UrlHeader . "." . $base64UrlPayload, $this->secretKey, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        // Verify it matches the signature provided in the token
        if ($base64UrlSignature !== $signatureProvided) {
            return null; // Invalid token
        }

        return json_decode($payload, true);
    }

    private function getAlgorithm() {
        $algos = [
            'HS256' => 'sha256',
            'HS384' => 'sha384',
            'HS512' => 'sha512',
        ];

        if (!isset($algos[$this->algorithm])) {
            throw new Exception('Algorithm not supported');
        }

        return $algos[$this->algorithm];
    }
}

// Usage example:
// $jwtHandler = new SimpleJWT('your-secret-key');
// $token = $jwtHandler->generateToken(['user_id' => 123]);
// $parsedToken = $jwtHandler->parseToken();

?>
