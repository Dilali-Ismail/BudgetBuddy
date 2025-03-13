<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{





    /**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Inscription d'un nouvel utilisateur",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password", "password_confirmation"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Utilisateur créé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ),
 *             @OA\Property(property="access_token", type="string", example="1|abcdef123456..."),
 *             @OA\Property(property="token_type", type="string", example="Bearer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Données invalides",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */
    public function register(Request $request){
        //validation

        $validator = Validator::make($request->all(),[
                 'name' => 'required|string|max:255',
                 'email' => 'required|string|email|max:255|',
                 'password' => 'required|string|max:255'
        ]);

        //message d'erreur de validation

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()],422);
        }

        //creation de user chmod 777 /var/www/html/storage/logs/laravel.log

        $user = User::create([
              'name' => $request->name ,
              'email' => $request->email,
              'password' => Hash::make($request->password)
        ]);

        //generer token

        $token = $user->createToken('auth_token')->plainTextToken;

        //returner le user Creer

        return response()->json([
            'user' => $user ,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ],201);

    }



/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Connexion d'un utilisateur existant",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Connexion réussie",
 *         @OA\JsonContent(
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ),
 *             @OA\Property(property="access_token", type="string", example="1|abcdef123456..."),
 *             @OA\Property(property="token_type", type="string", example="Bearer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Données invalides",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */


    public function login(Request $request){
        //validation de donnees
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|string',
            'password' => 'required'
        ]);

        //message erreur de validation

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()],422);
        }
         //recherche user par email
        $user = User::where('email' , $request->email)->first();

          // check si ce utilisateur exist
        if(!$user || ! Hash::check($request->password , $user->password)){
           throw ValidationException::withMessages([
            'message' => 'incorrect data'
           ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
             'user' => $user,
             'acees_token' => $token ,
             'token_type' => 'Bearer'
        ],);
    }


/**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Déconnexion et invalidation du token",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Déconnexion réussie",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Successfully logged out")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */


    public function logout(Request $request){
       $request->user()->currentAccessToken()->delete();
       return response()->json(['message'=> 'Successfuly logged out']);
    }

/**
 * @OA\Get(
 *     path="/api/user",
 *     summary="Récupération des informations de l'utilisateur",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Informations de l'utilisateur",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */


    public function user(Request $request){

        return response()->json($request->user());
    }
}
