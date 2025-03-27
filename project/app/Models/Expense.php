<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','title','amount','expense_date','group_id','methode_diviser'];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function participations()
    {
        return $this->hasMany(ExpenseParticipation::class);
    }
    public function isShared()
    {
    return $this->group_id !== null;
    }
}
