<?php
class JwtExtractor {
    public static function extractFromHeader() {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            throw new Exception("Authorization header tidak ditemukan.");
        }

        $authHeader = $headers['Authorization'];

        if (!str_starts_with($authHeader, 'Bearer ')) {
            throw new Exception("Format Authorization header tidak valid.");
        }

        return substr($authHeader, 7); // Ambil token setelah "Bearer "
    }
}
