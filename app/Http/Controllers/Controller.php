<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', title: 'Places API', description: 'API para gerenciamento de lugares (desafio backend SGBr).')]
#[OA\SecurityScheme(securityScheme: 'sanctum', type: 'http', scheme: 'bearer', bearerFormat: 'Sanctum personal access token')]
abstract class Controller
{
    //
}
