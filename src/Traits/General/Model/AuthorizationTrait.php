<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\General\Model;

use Illuminate\Support\Facades\Gate;

trait AuthorizationTrait
{
    public static function authorizeViewAny()
    {
        Gate::authorize('viewAny', get_called_class());
    }

    public static function authorizeCreate()
    {
        Gate::authorize('create', get_called_class());
    }

    public static function authorizeView($self)
    {
        Gate::authorize('view', $self);
    }

    public static function authorizeEdit($self)
    {
        Gate::authorize('update', $self);
    }

    public static function authorizeDelete($self)
    {
        Gate::authorize('delete', $self);
    }

    public static function authorizeRestore($self)
    {
        Gate::authorize('restore', $self);
    }

    public static function authorizeForceDelete($self)
    {
        Gate::authorize('forceDelete', $self);
    }
}