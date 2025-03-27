<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settle extends Model
{
    use HasFactory;

    protected $fillable = ['group_id','from_user_id', 'to_user_id','amount', 'status',];

    protected $casts = ['amount' => 'decimal:2',];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function isEnAttente()
    {
        return $this->status === 'en_attente';
    }

    public function isConfirmed(){
        return $this->status === 'confirmer';
    }

    public function isCanceled(){
        return $this->status === 'canceled';
    }
}
