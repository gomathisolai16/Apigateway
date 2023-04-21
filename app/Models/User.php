<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        // 'password',
        'token_response',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function findUserByEmail($email)
    {
        return static::where('email', $email)->get()->toArray();
    }
    public static function getManager($user_id)
    {
        return static::select(['id as value', DB::Raw("CONCAT(name, ' - ', email) AS label")])
        ->where('id', '!=', $user_id)->where('status', '=', 1)->get()->toArray();
    }
    public static function getManagerbyManagerIDs($userID,$managerIDs)
    {
        return static::select(['id as value', DB::Raw("CONCAT(name, ' - ', email) AS label")])
        ->where('id', '!=', $userID)->whereIN('id',$managerIDs)->where('status', '=', 1)->get()->toArray();
    }
    public static function getManagerIDbyManagerIDs($userID,$managerIDs)
    {
        return static::select('id')->where('id', '!=', $userID)->whereIN('id',$managerIDs)->where('status', '=', 1)->get()->pluck('id')->toArray();
    }

    public static function findUserById($id)
    {
        return static::where('id', $id)->first();
    }
}
