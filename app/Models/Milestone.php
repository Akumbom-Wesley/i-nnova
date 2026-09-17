<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Milestone extends Model
{
    use HasFactory;
    use HasSortOrder;
    use HasTranslations;

    public array $translatable = ['title', 'body'];

    protected $guarded = [];
}
