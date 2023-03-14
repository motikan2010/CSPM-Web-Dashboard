<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CspmStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'cloud_id',
        'exec_date',
        'response_status_code',
        'status'
    ];
}
