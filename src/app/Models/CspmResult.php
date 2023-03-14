<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CspmResult extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'cloud_id',
        'exec_date',
        'plugin',
        'category',
        'title',
        'description',
        'resource',
        'region',
        'status',
        'message',
    ];

}
