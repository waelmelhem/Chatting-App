<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Participant extends Pivot
{
    use HasFactory;
    public $timestamp=false;

protected $casts = ["joined_at"=>"datetime"];
}
