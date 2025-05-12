<?php

/**
 * @OA\Info(
 *     title="API",
 *     description="Restaurant API",
 *     version="1.0",
 *     @OA\Contact(
 *         email="web2001programming@gmail.com",
 *         name="Web Programming"
 *     )
 * )
 */

/**
 * @OA\Server(
 *      url="http://localhost/WEB-DEV-25-IBU/backend/rest",
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