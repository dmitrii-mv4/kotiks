<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $table = 'menu_items';
    protected $guarded = false;

    protected $fillable = [
        'menu_id',
        'title',
        'url',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
