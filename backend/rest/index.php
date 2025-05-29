<?php
// Autoload semua dependency Composer (termasuk Firebase JWT)
require_once __DIR__ . '/vendor/autoload.php';

// Service
require_once __DIR__ . '/services/AuthService.php';

// FlightPHP Service registration
Flight::register('auth_service', 'AuthService');

// ROUTES
require_once __DIR__ . '/routes/AuthRoutes.php';
require_once __DIR__ . '/routes/MaterialRoutes.php';
require_once __DIR__ . '/routes/OptionItemRoutes.php';
require_once __DIR__ . '/routes/QuestionRoutes.php';
require_once __DIR__ . '/routes/QuizRoutes.php';
require_once __DIR__ . '/routes/TextMaterialRoutes.php';
require_once __DIR__ . '/routes/UserRoutes.php';
require_once __DIR__ . '/routes/StudentAnswerRoutes.php';


// Opsional: aktifkan logging error di FlightPHP (sangat membantu untuk debugging)
Flight::set('flight.log_errors', true);

// Jalankan aplikasi
Flight::start();
