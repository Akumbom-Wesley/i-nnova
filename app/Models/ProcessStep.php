<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * A step in "how we work", shown on the About page.
 */
class ProcessStep extends Model
{
    use HasFactory;
    use HasSortOrder;
    use HasTranslations;

    public array $translatable = ['title', 'summary', 'body'];

    protected $guarded = [];
}
