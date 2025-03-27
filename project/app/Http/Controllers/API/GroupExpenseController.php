<?php

namespace App\Http\Controllers\API;

use App\Models\Group;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ExpenseParticipation;
use App\Http\Resources\ExpenseResource;
use Illuminate\Support\Facades\Validator;

class GroupExpenseController extends Controller
{
   public function index(Request $request , $groupId){

    $group = Group::findOrFail($groupId);
    if (!$group->users->contains($request->user()->id)) {
        return response()->json([
            'message' => 'Vous n\'êtes pas membre de ce groupe.'
        ], 403);
    }

    $expenses = Expense::where('group_id', $groupId)
    ->with(['participations.user'])
    ->get();

   return ExpenseResource::collection($expenses);

   }

   public function store(Request $request, $groupId)
   {
      
       $group = Group::with('users')->findOrFail($groupId);


       if (!$group->users->contains($request->user()->id)) {
           return response()->json([
               'message' => 'Vous n\'êtes pas membre de ce groupe.'
           ], 403);
       }


       $validator = Validator::make($request->all(), [
           'title' => 'required|string|max:255',
           'amount' => 'required|numeric|min:0.01',
           'expense_date' => 'required|date',
           'methode_diviser' => 'required|in:equal,percentage,amount',
           'payeur' => 'required|array|min:1',
           'payeur.*.user_id' => 'required|exists:users,id',
           'payeur.*.amount' => 'required_if:methode_diviser,amount|numeric|min:0',
           'payeur.*.percentage' => 'required_if:methode_diviser,percentage|numeric|min:0|max:100',
           'beneficier' => 'required|array|min:1',
           'beneficier.*.user_id' => 'required|exists:users,id',
           'beneficier.*.amount' => 'required_if:methode_diviser,amount|numeric|min:0',
           'beneficier.*.percentage' => 'required_if:methode_diviser,percentage|numeric|min:0|max:100',
       ]);

       if ($validator->fails()) {
           return response()->json(['errors' => $validator->errors()], 422);
       }


       $memberIds = $group->users->pluck('id')->toArray();
       $payerIds = collect($request->payeur)->pluck('user_id')->toArray();
       $beneficiaryIds = collect($request->beneficier)->pluck('user_id')->toArray();

       $nonMemberPayers = array_diff($payerIds, $memberIds);
       $nonMemberBeneficiaries = array_diff($beneficiaryIds, $memberIds);

       if (!empty($nonMemberPayers) || !empty($nonMemberBeneficiaries)) {
           return response()->json([
               'message' => 'Tous les payeurs et bénéficiaires doivent être membres du groupe.'
           ], 422);
       }


       if ($request->methode_diviser === 'amount') {

           $totalPaid = collect($request->payeur)->sum('amount');
           if (abs($totalPaid - $request->amount) > 0.01) {
               return response()->json([
                   'message' => 'La somme des montants payés doit être égale au montant total de la dépense.'
               ], 422);
           }


           $totalBenefited = collect($request->beneficier)->sum('amount');
           if (abs($totalBenefited - $request->amount) > 0.01) {
               return response()->json([
                   'message' => 'La somme des montants bénéficiés doit être égale au montant total de la dépense.'
               ], 422);
           }
       } elseif ($request->methode_diviser === 'percentage') {

           $totalPaidPercentage = collect($request->payeur)->sum('percentage');
           if (abs($totalPaidPercentage - 100) > 0.01) {
               return response()->json([
                   'message' => 'La somme des pourcentages payés doit être égale à 100%.'
               ], 422);
           }


           $totalBenefitedPercentage = collect($request->beneficier)->sum('percentage');
           if (abs($totalBenefitedPercentage - 100) > 0.01) {
               return response()->json([
                   'message' => 'La somme des pourcentages bénéficiés doit être égale à 100%.'
               ], 422);
           }
       }

       $expense = new Expense();
       $expense->title = $request->title;
       $expense->amount = $request->amount;
       $expense->expense_date = $request->expense_date;
       $expense->user_id = $request->user()->id;
       $expense->group_id = $group->id;
       $expense->methode_diviser = $request->methode_diviser;
       $expense->save();


       foreach ($request->payeur as $payer) {
           $payerParticipation = new ExpenseParticipation();
           $payerParticipation->expense_id = $expense->id;
           $payerParticipation->user_id = $payer['user_id'];
           $payerParticipation->type = 'payeur';

           if ($request->methode_diviser === 'equal') {

               $payerParticipation->amount = $request->amount / count($request->payeur);
               $payerParticipation->percentage = 100 / count($request->payeur);
           } elseif ($request->methode_diviser === 'percentage') {

               $payerParticipation->percentage = $payer['percentage'];
               $payerParticipation->amount = ($payer['percentage'] / 100) * $request->amount;
           } else {
               $payerParticipation->amount = $payer['amount'];
               $payerParticipation->percentage = ($payer['amount'] / $request->amount) * 100;
           }

           $payerParticipation->save();
       }


       foreach ($request->beneficier as $beneficiary) {
           $beneficiaryParticipation = new ExpenseParticipation();
           $beneficiaryParticipation->expense_id = $expense->id;
           $beneficiaryParticipation->user_id = $beneficiary['user_id'];
           $beneficiaryParticipation->type = 'benificier';

           if ($request->methode_diviser === 'equal') {

               $beneficiaryParticipation->amount = $request->amount / count($request->beneficier);
               $beneficiaryParticipation->percentage = 100 / count($request->beneficier);
           } elseif ($request->methode_diviser === 'percentage') {

               $beneficiaryParticipation->percentage = $beneficiary['percentage'];
               $beneficiaryParticipation->amount = ($beneficiary['percentage'] / 100) * $request->amount;
           } else {

               $beneficiaryParticipation->amount = $beneficiary['amount'];
               $beneficiaryParticipation->percentage = ($beneficiary['amount'] / $request->amount) * 100;
           }

           $beneficiaryParticipation->save();
       }


       $expense->load('participations.user');

       return new ExpenseResource($expense);
   }

 public function destroy(Request $request, $groupId, $expenseId){

    $group = Group::findOrFail($groupId);
    if (!$group->users->contains($request->user()->id)) {
        return response()->json([
            'message' => 'Vous n\'êtes pas membre de ce groupe.'
        ], 403);
    }

    $expense = Expense::where('id',$expenseId)->where('group_id',$groupId)->first();

    if (!$expense) {
        return response()->json([
            'message' => 'Dépense non trouvée dans ce groupe.'
        ], 404);
    }

    if ($expense->user_id !== $request->user()->id && $group->admin_id !== $request->user()->id) {
        return response()->json([
            'message' => 'Vous n\'êtes pas autorisé à supprimer cette dépense.'
        ], 403);
    }

    $expense->delete();
    return response()->json([
        'message' => 'Supprimer avec success'
    ]);
 }

}
