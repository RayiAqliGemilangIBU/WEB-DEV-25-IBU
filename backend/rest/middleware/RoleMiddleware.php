<?php
class RoleMiddleware {
    public static function requireRole($requiredRole) {
        return function () use ($requiredRole) {
            $decoded = Flight::get('jwt_decoded');
            $userRole = $decoded->role ?? null;

            if ($userRole !== $requiredRole) {
                Flight::halt(403, json_encode(['error' => 'Access forbidden: insufficient privileges']));
            }
        };
    }

    public static function allowRoles(array $allowedRoles) {
        return function () use ($allowedRoles) {
            $decoded = Flight::get('jwt_decoded');
            $userRole = $decoded->role ?? null;

            if (!in_array($userRole, $allowedRoles)) {
                Flight::halt(403, json_encode(['error' => 'Access forbidden: insufficient privileges']));
            }
        };
    }
}
