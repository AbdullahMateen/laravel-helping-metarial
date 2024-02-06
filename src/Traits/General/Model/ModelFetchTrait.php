<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\General\Model;

use Illuminate\Support\Facades\DB;

trait ModelFetchTrait
{
    public static function getNextGeneratedId()
    {
        if (config('database.default') === 'pgsql') {
            $self = new static();
            if (!$self->getIncrementing()) {
                throw new \Exception(sprintf('Model (%s) is not auto-incremented', static::class));
            }
            $sequenceName = "{$self->getTable()}_id_seq";
            return DB::selectOne("SELECT nextval('{$sequenceName}') AS val")->val;
        }

        $statement = DB::select("SHOW TABLE STATUS LIKE '" . get_model_table(get_called_class()) ."'");
        return $statement[0]->Auto_increment;
    }

    /* ==================== Exists ==================== */
    public static function recordExists($value, $by = 'slug', $operator = '=')
    {
        return self::where($by, $operator, $value)->exists();
    }

    public static function anyRecordsExists(array $values, $by = 'slug')
    {
        $count = self::whereIn($by, $values)->count();
        return $count > 0;
    }

    public static function allRecordsExists(array|null $values, $by = 'slug')
    {
        $values = $values ?? [];
        $count = self::whereIn($by, $values)->count();
        return $count == count($values);
    }

    public static function allRecordsNotExists(array $values, $by = 'slug')
    {
        $count = self::whereIn($by, $values)->count();
        return $count < 1;
    }

    /* ==================== Fetch ==================== */
    public static function fetchRecordId($value, $by = 'slug', $operator = '=')
    {
        return self::where($by, $operator, $value)->pluck('id')->first();
    }

    public static function fetchRecordsIds(array $values, $by = 'slug')
    {
        return self::whereIn($by, $values)->pluck('id')->all();
    }

    public static function fetchRecord($value, $by = 'slug', $operator = '=')
    {
        return self::where($by, $operator, $value)->first();
    }

    public static function fetchRecords($values, $by = 'slug', $operator = '=')
    {
        $values = is_array($values) ? $values : explode(',', $values);
        return self::whereIn($by, $values)->get();
    }

    public static function fetchRandomRecord()
    {
        return self::inRandomOrder()->first();
    }

    public static function fetchRandomRecords($total = 10)
    {
        return self::inRandomOrder()->take($total)->get();
    }

    public static function fetchRandomId()
    {
        return self::inRandomOrder()->pluck('id')->first();
    }

    public static function fetchRandomIds($total = 10)
    {
        return self::inRandomOrder()->take($total)->pluck('id')->all();
    }

    /* ==================== Increment/Decrement ==================== */
    public static function incrementColumnBy($recordId, $column = 'usedBy', $amount = 1)
    {
        return self::where('id', '=', $recordId)->increment($column, $amount);
    }

    public static function decrementColumnBy($recordId, $column = 'usedBy', $amount = 1)
    {
        return self::where('id', '=', $recordId)->decrement($column, $amount);
    }
}
