<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRegistration extends Model
{
    use HasFactory;
    Protected $fillable=[
        'jobId',
        'userId',
        'firstName',
        'lastName',
        'dob',
        'age',
        'ssn',
        'city',
        'state',
        'zipcode',
        'phone',
        'educationLevel',
        'uploadResume',
        'employmentHistory',
        'languageKnown',
        'gender',
        'disability',
        'veteranStatus',
        'openToRelocation',
        'signature',
        'isActive',
    ];
}
