<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuType extends Model
{
    use HasFactory;

    protected $primaryKey = 'menu_type_id';

    protected $fillable = [
        'type_name',
        'order_index',
        'is_active'
    ];
}
