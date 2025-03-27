<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name','devise','description','admin_id'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function settles()
    {
        return $this->hasMany(Settle::class);
    }



}
