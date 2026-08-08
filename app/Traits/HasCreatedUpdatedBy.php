<?php

namespace App\Traits;

trait HasCreatedUpdatedBy
{
    public static function bootHasCreatedUpdatedBy(): void
    {
        static::creating(function ($model) {
            if (empty($model->created_by) && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
