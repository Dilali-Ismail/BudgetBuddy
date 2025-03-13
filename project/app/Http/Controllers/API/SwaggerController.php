<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="BudgetBuddy API Documentation",
 *     version="1.0.0",
 *     description="API pour la gestion des dépenses personnelles et des tags",
 *     @OA\Contact(
 *         email="contact@budgetbuddy.com",
 *         name="Support API BudgetBuddy"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="BudgetBuddy API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

class SwaggerController extends Controller
{
     
}
