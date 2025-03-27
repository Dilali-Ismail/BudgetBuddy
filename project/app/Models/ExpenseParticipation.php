<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseParticipation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','expence_id' ,'type','amount','percentage'];


    // les attribut que je dois convertire en string
    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    public function expense(){
        return $this->belongsTo(Expense::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function isPayer()
    {
        return $this->type === 'payer';
    }

    public function isBenificier()
    {
        return $this->type === 'benificier';
    }

}
