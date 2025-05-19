<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    Protected $fillable=[
        'jobImage',
        'jobName',
        'companyName',
        'postDate',
        'applyBefore',
        'jobNature',
        'salaryRange',
        'jobLocation',
        'jobDescription',
        'roleAndResponcibility',
        'isActive',
        'phone',
        'minSalary',
        'maxSalary',
        'salaryFrequency',
    ];
}
