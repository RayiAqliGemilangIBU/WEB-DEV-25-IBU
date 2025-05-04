<?php

/**
 * @OA\Info(
 *     title="API",
 *     description="ChhemLP",
 *     version="1.0",
 *     @OA\Contact(
 *         email="web2001programming@gmail.com",
 *         name="Web Programming"
 *     )
 * )
 */

/**
 * @OA\Server(
 *      url="http://localhost/wWEB-DEV-25-IBU/backend",
 *      description="API server"
 * )
 */

/**
 * @OA\SecurityScheme(
 *     securityScheme="ApiKey",
 *     type="apiKey",
 *     in="header",
 *     name="Authentication"
 * )
 */

 /**
 * @OA\OpenApi(
 *     openapi="3.0.0",
 *     info={
 *         "title"="API Documentation",
 *         "version"="1.0.0"
 *     }
 * )
 */
$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/doc_setup.php',  // Memastikan file ini benar
    __DIR__ . '/../../rest/index.php'  // Pastikan path ini juga benar
]);

header('Content-Type: application/json');
echo $openapi->toJson();