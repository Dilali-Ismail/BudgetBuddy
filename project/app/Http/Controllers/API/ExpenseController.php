<?php

namespace App\Http\Controllers\API;

use App\Models\Expense;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpenseResource;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
 * @OA\Get(
 *     path="/api/expenses",
 *     summary="Liste toutes les dépenses de l'utilisateur authentifié",
 *     tags={"Expenses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des dépenses",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Courses supermarché"),
 *                 @OA\Property(property="description", type="string", example="Achats hebdomadaires"),
 *                 @OA\Property(property="amount", type="number", format="float", example=85.50),
 *                 @OA\Property(property="expense_date", type="string", format="date", example="2025-03-12"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time"),
 *                 @OA\Property(property="tags", type="array", @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Alimentation"),
 *                     @OA\Property(property="color", type="string", example="#FF5733"),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time")
 *                 ))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */

    public function index(Request $request)
    {
        $this->authorize('viewAny',Expense::class);
        $expenses = $request->user()->expenses;
        return ExpenseResource::collection($expenses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
 * @OA\Post(
 *     path="/api/expenses",
 *     summary="Crée une nouvelle dépense",
 *     tags={"Expenses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "amount", "expense_date"},
 *             @OA\Property(property="title", type="string", example="Courses supermarché"),
 *             @OA\Property(property="description", type="string", example="Achats hebdomadaires"),
 *             @OA\Property(property="amount", type="number", format="float", example=85.50),
 *             @OA\Property(property="expense_date", type="string", format="date", example="2025-03-12")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Dépense créée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Courses supermarché"),
 *             @OA\Property(property="description", type="string", example="Achats hebdomadaires"),
 *             @OA\Property(property="amount", type="number", format="float", example=85.50),
 *             @OA\Property(property="expense_date", type="string", format="date", example="2025-03-12"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Données invalides",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */

    public function store(Request $request)
    {

        //ici je donne l'autorisation
        $this->authorize('create',Expense::class);

        //validation
        $validator = Validator::make($request->all(),[
              'title' => 'required|string|max:255',
              'amount' => 'required|numeric|min:0',
              'expense_date' => 'required|date'
        ]);
        if($validator->fails()){
            return response()->json(['erreur' => $validator->errors()],'422');
        }

       $expense = $request->user()->expenses()->create([
            'title' => $request->title ,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
        ]);

         return new ExpenseResource($expense);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

/**
 * @OA\Get(
 *     path="/api/expenses/{id}",
 *     summary="Affiche une dépense spécifique",
 *     tags={"Expenses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la dépense",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Détails de la dépense",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Courses supermarché"),
 *             @OA\Property(property="description", type="string", example="Achats hebdomadaires"),
 *             @OA\Property(property="amount", type="number", format="float", example=85.50),
 *             @OA\Property(property="expense_date", type="string", format="date", example="2025-03-12"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time"),
 *             @OA\Property(property="tags", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Alimentation"),
 *                 @OA\Property(property="color", type="string", example="#FF5733"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dépense non trouvée"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès interdit"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */


    public function show(Expense $expense)
    {
        $this->authorize('view',$expense);
        return new ExpenseResource($expense);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
 * @OA\Put(
 *     path="/api/expenses/{id}",
 *     summary="Met à jour une dépense spécifique",
 *     tags={"Expenses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la dépense",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "amount", "expense_date"},
 *             @OA\Property(property="title", type="string", example="Courses supermarché - Modifié"),
 *             @OA\Property(property="description", type="string", example="Achats hebdomadaires bio"),
 *             @OA\Property(property="amount", type="number", format="float", example=95.50),
 *             @OA\Property(property="expense_date", type="string", format="date", example="2025-03-12")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Dépense mise à jour avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Courses supermarché - Modifié"),
 *             @OA\Property(property="description", type="string", example="Achats hebdomadaires bio"),
 *             @OA\Property(property="amount", type="number", format="float", example=95.50),
 *             @OA\Property(property="expense_date", type="string", format="date", example="2025-03-12"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Données invalides",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dépense non trouvée"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès interdit"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update',$expense);

        //validation
        $validator = Validator::make($request->all(),[
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date'
      ]);
      if($validator->fails()){
          return response()->json(['erreur' => $validator->errors()],'422');
      }

      $expense->update([
            'title' => $request->title ,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
      ]);

      return new ExpenseResource($expense);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

/**
 * @OA\Delete(
 *     path="/api/expenses/{id}",
 *     summary="Supprime une dépense spécifique",
 *     tags={"Expenses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la dépense",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Dépense supprimée avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dépense non trouvée"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès interdit"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */

    public function destroy(Expense $expense)
    {
        $this->authorize('delete',$expense);
        $expense->delete();
        return  response()->json(null,204);
    }

    /**
 * Associer des tags à une dépense.
 */


/**
 * @OA\Post(
 *     path="/api/expenses/{id}/tags",
 *     summary="Associe des tags à une dépense",
 *     tags={"Expenses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la dépense",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"tags"},
 *             @OA\Property(property="tags", type="array", @OA\Items(type="integer"), example={1, 2})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Tags associés avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Courses supermarché"),
 *             @OA\Property(property="description", type="string", example="Achats hebdomadaires"),
 *             @OA\Property(property="amount", type="number", format="float", example=85.50),
 *             @OA\Property(property="expense_date", type="string", format="date", example="2025-03-12"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time"),
 *             @OA\Property(property="tags", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Alimentation"),
 *                 @OA\Property(property="color", type="string", example="#FF5733"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ))
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Données invalides",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dépense non trouvée"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès interdit"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */

public function attachTags(Request $request, Expense $expense)
{
    
    $this->authorize('update', $expense);

    // Validation des données
    $validator = Validator::make($request->all(), [
        'tags' => 'required|array',
        'tags.*' => 'exists:tags,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }


    $userTagIds = $request->user()->tags()->whereIn('id', $request->tags)->pluck('id')->toArray();
    $expense->tags()->sync($userTagIds);

    return new ExpenseResource($expense->load('tags'));
}
}
