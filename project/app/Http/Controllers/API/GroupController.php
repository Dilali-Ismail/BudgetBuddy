<?php

namespace App\Http\Controllers\API;

use App\Models\Group;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\ExpenseParticipation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Nette\Utils\Json;

class GroupController extends Controller
{


  public function index(Request $request){
    $this->authorize('viewAny',Group::class);
    $groups = $request->user()->groups;
    return GroupResource::collection($groups);
  }



    public function store(Request $request){

        $this->authorize('create',Group::class);

        $validator = Validator::make($request->all(),[
             'name' => 'required|string|max:255',
             'description' => 'required|string|max:255',
             'devise' => 'required|string|max:255',
             'members' => 'nullable|array',
             'members.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $group = new Group();
        $group->name = $request->name;
        $group->devise = $request->devise ?? 'MAD';
        $group->description = $request->description;
        $group->admin_id = $request->user()->id;
        $group->save();

        $group->users()->attach($request->user()->id);

        if ($request->has('members') && is_array($request->members)) {
            foreach ($request->members as $memberId) {
                if ($memberId != $request->user()->id) {
                    $group->users()->attach($memberId);
                }
            }
        }

        $group->load(['users', 'admin']);

        return new GroupResource($group);
    }

    public function show(Request $request, $id)
    {

        $group = Group::with(['users', 'admin'])->findOrFail($id);


        if (!$group->users->contains($request->user()->id)) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à voir ce groupe.'
            ], 403);
        }

        $group->load('expenses');

        return new GroupResource($group);
    }


    public function destroy(Request $request, $id){

        $group = Group::findorfail($id);
        $this->authorize('delete', $group);
        $hasPendingBalances = $this->hasPendingBalances($group);
        if ($hasPendingBalances) {
            return response()->json([
                'message' => 'Ce groupe ne peut pas être supprimé car il y a encore des soldes à régler.'
            ], 422);
        }
        $group->delete();
        return response()->json(null, 204);

    }

    private function hasPendingBalances(Group $group)
    {
        return false;
    }

    public function balances(Request $request, $id){

      $group = Group::with('users')->findOrFail($id);

      if(!$group->users->contains($request->user()->id)){
        return response()->json(['message' => 'Vous n\'êtes pas membre de ce groupe.'], 403);
      }

      $expenses = Expense::where('group_id',$id)->with(['participations.user'])->get();
      $balances = $this->calculatesBalances($group,$expenses);
      $transactions = $this->calculateTransactions($balances);

      return response()->json([

        'balances' => $balances,
        'transactions' => $transactions,
        'statistics' => [
            'total_expenses' => $expenses->sum('amount'),
            'expense_count' => $expenses->count(),
        ]]);
}


    private function calculatesBalances(Group $group,$expenses){

        $balances = [] ;


       foreach($group->users as $user)
       {
           $balances[$user->id] = [

             'user_id' => $user->id,
             'user_name' => $user->name,
             'user_email' => $user->email,
             'paid' => 0 ,
             'benifited' => 0 ,
             'balance' => 0
           ];


           foreach($expenses as $expense){
            foreach($expense->participations as $participation){

                if (!isset($balances[$participation->user_id])) {
                    continue;
                }
                if($participation->type === 'payeur'){
                    $balances[$user->id]['paid'] += $participation->amount;
                }elseif($participation->type === 'benificier'){
                    $balances[$user->id]['benifited'] += $participation->amount;
                }
            }

            foreach($balances as &$balance){
                $balance['balance'] = $balance['paid'] - $balance['benifited'];
                $balance['balance'] = round($balance['balance'],2);
            }

            return array_values($balances);
           }
       }

    }

   private function calculateTransactions($balances){
     $debtors = [];
     $creditors = [];

     foreach($balances as $balance){
        if($balance['balance'] < -0.01){
            $debtores[]= [
                'user_id' => $balance['user_id'],
                'name' => $balance['name'],
                'balance' => $balance['balance']
            ];
        }elseif($balance['balance'] > -0.01){
            $creditors[] = [
                'user_id' => $balance['user_id'],
                'name' => $balance['user_name'],
                'balance' => $balance['balance']
            ];
        }
     }

     usort($debtors, function ($a, $b) {
        return $a['balance'] <=> $b['balance'];
    });

    usort($creditors, function ($a, $b) {
        return $b['balance'] <=> $a['balance'];
    });

    $transactions = [];

    while (count($debtors) > 0 && count($creditors) > 0) {
        $debtor = $debtors[0];
        $creditor = $creditors[0];


        $amount = min(abs($debtor['balance']), $creditor['balance']);
        $amount = round($amount, 2);

        if ($amount > 0.01) {
            $transactions[] = [
                'from_user_id' => $debtor['user_id'],
                'from_name' => $debtor['name'],
                'to_user_id' => $creditor['user_id'],
                'to_name' => $creditor['name'],
                'amount' => $amount
            ];
        }

        $debtor['balance'] += $amount;
        $creditor['balance'] -= $amount;


        if (abs($debtor['balance']) < 0.01) {
            array_shift($debtors);
        } else {
            $debtors[0] = $debtor;
        }

        if ($creditor['balance'] < 0.01) {
            array_shift($creditors);
        } else {
            $creditors[0] = $creditor;
        }
    }



   }


}
