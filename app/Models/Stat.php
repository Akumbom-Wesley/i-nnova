<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Stat extends Model
{
    use HasFactory;
    use HasSortOrder;
    use HasTranslations;

    public const CONTEXT_SITE = 'site';

    public const CONTEXT_KICKSTARTER = 'kickstarter';

    public array $translatable = ['label', 'caption'];

    protected $guarded = [];

    public function scopeContext(Builder $query, string $context): Builder
    {
        return $query->where('context', $context);
    }
}
