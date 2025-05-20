<?php
require_once __DIR__ . '/vendor/autoload.php';

// // Inisialisasi FlightPHP
// Flight::set('flight.log_errors', true);

// Load semua route
require_once __DIR__ . '/routes/MaterialRoutes.php';
require_once __DIR__ . '/routes/OptionItemRoutes.php';
require_once __DIR__ . '/routes/QuestionRoutes.php';
require_once __DIR__ . '/routes/QuizRoutes.php';
require_once __DIR__ . '/routes/TextMaterialRoutes.php';
require_once __DIR__ . '/routes/UserRoutes.php';

// Mulai aplikasi
Flight::start();
