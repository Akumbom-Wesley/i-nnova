<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasSortOrder
{
    /**
     * Every content model is hand-ordered in the admin, so the front end can
     * always call ->ordered() and get what the editor arranged.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
