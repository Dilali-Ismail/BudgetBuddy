<?php

namespace App\Http\Controllers\API;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


/**
 * @OA\Get(
 *     path="/api/tags",
 *     summary="Liste tous les tags de l'utilisateur authentifié",
 *     tags={"Tags"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des tags",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Alimentation"),
 *                 @OA\Property(property="color", type="string", example="#FF5733"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
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
           $this->authorize('viewAny',Tag::class);
           $tags = $request->user()->tags ;
           return TagResource::collection($tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    /**
 * @OA\Post(
 *     path="/api/tags",
 *     summary="Crée un nouveau tag",
 *     tags={"Tags"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Alimentation"),
 *             @OA\Property(property="color", type="string", example="#FF5733")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Tag créé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Alimentation"),
 *             @OA\Property(property="color", type="string", example="#FF5733"),
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
        $this->authorize('create',Tag::class);

        $validator = Validator::make($request->all(),[
              'name' => 'required|string|max:255',
              'color' => 'nullable|string|max:10'
        ]);

        if($validator->fails()){
        return response()->json(['erreur',$validator->errors()],422);
        }

        $tag = $request->user()->tags()->create([
            'name' => $request->name,
            'color' => $request->color ?? '#000000',
        ]);

        return new TagResource($tag);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
 * @OA\Get(
 *     path="/api/tags/{id}",
 *     summary="Affiche un tag spécifique",
 *     tags={"Tags"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID du tag",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Détails du tag",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Alimentation"),
 *             @OA\Property(property="color", type="string", example="#FF5733"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Tag non trouvé"
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



    public function show(Tag $tag)
    {
        $this->authorize('view',$tag);

        return new TagResource($tag);

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
 *     path="/api/tags/{id}",
 *     summary="Met à jour un tag spécifique",
 *     tags={"Tags"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID du tag",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Alimentation Bio"),
 *             @OA\Property(property="color", type="string", example="#33FF57")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Tag mis à jour avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Alimentation Bio"),
 *             @OA\Property(property="color", type="string", example="#33FF57"),
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
 *         description="Tag non trouvé"
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


    public function update(Request $request, Tag $tag)
    {
          $this->authorize('update',$tag);

          $validator = Validator::make($request->all(),[
              'name' => 'required|string|max:255',
              'color' => 'nullable|string|max:10'
          ]);

          if($validator->fails()){
            return response()->json(['erreur',$validator->errors()],422);
            }

            $tag->name = $request->name;
            $tag->color = $request->color ?? $tag->color;
            $tag->save();
            $tag = $tag->fresh();

            return new TagResource($tag);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
 * @OA\Delete(
 *     path="/api/tags/{id}",
 *     summary="Supprime un tag spécifique",
 *     tags={"Tags"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID du tag",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Tag supprimé avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Tag non trouvé"
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



    public function destroy(Tag $tag)
    {
        $this->authorize('delete',$tag);
        $tag->delete();
        return response()->json(null,204);

    }
}
