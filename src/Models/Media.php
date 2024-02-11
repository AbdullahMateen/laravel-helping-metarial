<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends ExtendedModel
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Override Methods
    |--------------------------------------------------------------------------
    */

    public static function boot()
    {
        static::creating(function ($self) {
            // unset($self->status);
        });

        parent::boot();
    }

    /*
    |--------------------------------------------------------------------------
    | Contracts / Traits methods
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Const Variables
    |--------------------------------------------------------------------------
    */

//    public const KEY_DISK_TEMP         = 'temp';
//    public const KEY_DISK_PROJECT      = 'project';
//    public const KEY_DISK_PLACEHOLDERS = 'placeholders';
//    public const KEY_DISK_PROFILE      = 'profile';
//
//    public const DISK_TEMP         = self::KEY_DISK_TEMP;
//    public const DISK_PROJECT      = self::KEY_DISK_PROJECT;
//    public const DISK_PLACEHOLDERS = self::KEY_DISK_PLACEHOLDERS;
//    public const DISK_PROFILE      = self::KEY_DISK_PROFILE;
//    public const DISKS             = [
//        self::KEY_DISK_TEMP         => self::DISK_TEMP,
//        self::KEY_DISK_PROJECT      => self::DISK_PROJECT,
//        self::KEY_DISK_PLACEHOLDERS => self::DISK_PLACEHOLDERS,
//        self::KEY_DISK_PROFILE      => self::DISK_PROFILE,
//    ];
//
//    public const KEY_GROUP_TEMP         = 'temp';
//    public const KEY_GROUP_PROJECT      = 'project';
//    public const KEY_GROUP_PLACEHOLDERS = 'placeholders';
//    public const KEY_GROUP_PROFILE      = 'profile';
//
//    public const KEY_CATEGORY_IMAGE    = 'image';
//    public const KEY_CATEGORY_VIDEO    = 'video';
//    public const KEY_CATEGORY_DOCUMENT = 'document';
//    public const KEY_CATEGORY_ARCHIVE  = 'archive';

    /*
    |--------------------------------------------------------------------------
    | Scope Methods
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Validations
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Helper Functions
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function mediaable()
    {
        return $this->morphTo();
    }
}