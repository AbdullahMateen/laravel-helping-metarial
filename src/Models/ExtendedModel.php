<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Models;

use AbdullahMateen\LaravelHelpingMaterial\Enums\StatusEnum;
use AbdullahMateen\LaravelHelpingMaterial\Interfaces\ColorsCodeInterface;
use AbdullahMateen\LaravelHelpingMaterial\Traits\General\Model\AuthorizationTrait;
use AbdullahMateen\LaravelHelpingMaterial\Traits\General\Model\ModelFetchTrait;
use AbdullahMateen\LaravelHelpingMaterial\Traits\General\Model\ValidationRulesTrait;
use AbdullahMateen\LaravelHelpingMaterial\Traits\General\Model\ValidationTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static columns()
 * @method static auth()
 * @method static active()
 * @method static inactive()
 */
class ExtendedModel extends Model implements ColorsCodeInterface
{
    use AuthorizationTrait, ModelFetchTrait, ValidationRulesTrait, ValidationTrait;

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    protected $guarded = [];

    protected $casts = [
        'status' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Override Methods
    |--------------------------------------------------------------------------
    */

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes(array_merge($this->attributes, [
            'status' => StatusEnum::Active->value,
        ]), true);
        parent::__construct($attributes);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | Const Variables
    |--------------------------------------------------------------------------
    */

    /* ================= Pages ======================== */
    public const KEY_PAGE_TYPE  = 'pageType';
    public const KEY_PAGE_INDEX = 'index';
    public const KEY_PAGE_TRASH = 'trash';

    /* ================= Forms ======================== */
    public const KEY_FORM_TYPE        = 'formType';
    public const KEY_FORM_TYPE_CREATE = 'create';
    public const KEY_FORM_TYPE_EDIT   = 'edit';

    /*
    |--------------------------------------------------------------------------
    | Scope Methods
    |--------------------------------------------------------------------------
    */

    public function scopeColumns($query, $columns = [], $overwrite = false)
    {
        $default = ['id', 'name'];
        $columns = is_array($columns) ? $columns : explode(',', $columns);
        $columns = $overwrite ? $columns : array_merge($default, $columns);
        return $query->select($columns);
    }

    public function scopeAuth($query, $columnName = 'user_id')
    {
        if (!auth_check()) return $query;
        return $query->where($columnName, '=', auth_id());
    }

    public function scopeActive($query)
    {
        return $query->where(get_model_table(get_called_class()) . '.status', '=', StatusEnum::Active->value);
    }

    public function scopeInactive($query)
    {
        return $query->where(get_model_table(get_called_class()) . '.status', '=', StatusEnum::InActive->value);
    }

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

    public function readable()
    {
        $this->statusName  = method_exists($this, 'statusName') ? $this->statusName() : '';
        $this->statusColor = method_exists($this, 'statusColor') ? $this->statusColor() : '';

        return $this;
    }

    public function deletable()
    {
        return true;
    }

    public function deleteInstance()
    {
        $this->delete();
    }

    public function restorable()
    {
        return true;
    }

    public function restoreInstance()
    {
        return $this->restore();
    }

    public function forceDeletable()
    {
        return true;
    }

    public function forceDeleteInstance()
    {
        return $this->forceDelete();
    }

    public function statusName()
    {
        return !($this->status instanceof StatusEnum) ? StatusEnum::tryFrom($this->status)?->toString() : $this->status->toString();
    }

    public function statusColor()
    {
        return !($this->status instanceof StatusEnum) ? StatusEnum::tryFrom($this->status)?->color() : $this->status->color();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


}
