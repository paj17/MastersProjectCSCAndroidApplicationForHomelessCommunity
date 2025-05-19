<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable
{
    use HasFactory;
    protected $guard="users";
    protected $fillable = [
        'userName',
        'address',
        'email',
        'profileImage',
		'isActive',
        'userToken',
        'password',
    ];
}
