<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $table = 'wr_board';

    protected $fillable = [
        'board_id',
        'board_type',
        'board_tlte',
        'board_user_id',
        'create_date',
        'views_count',
    ];

    protected $appends = [
        'full_name'
    ];

    public function getFullNameAttribute()
    {
        return $this->name . " 님";
    }
}
