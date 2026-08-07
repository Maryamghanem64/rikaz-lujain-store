<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'store_name_ar',
    'phone',
    'whatsapp',
    'instagram_url',
    'whish_number',
    'currency',
    'reservation_hours',
    'about_text_ar',
    'policy_text_ar',
])]
class Setting extends Model
{
    protected function casts(): array
    {
        return [
            'reservation_hours' => 'integer',
        ];
    }
}
