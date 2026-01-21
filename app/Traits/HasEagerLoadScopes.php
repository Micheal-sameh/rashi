<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasEagerLoadScopes
{
    /**
     * Scope to eager load common relationships for API responses
     */
    public function scopeWithApiRelations(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Scope to eager load common relationships for web views
     */
    public function scopeWithWebRelations(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Scope to eager load minimal data for dropdowns
     */
    public function scopeForDropdown(Builder $query): Builder
    {
        return $query;
    }
}
