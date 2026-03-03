<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'heading',
        'sub_heading',
        'image',
    ];
}
