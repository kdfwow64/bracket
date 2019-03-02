<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model {

    protected $table = 'question';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

}
