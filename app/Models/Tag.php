<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table      = 't_tags';
    protected $primaryKey = 'id';
    protected $guarded    = [];
    public $timestamps    = false;
}
