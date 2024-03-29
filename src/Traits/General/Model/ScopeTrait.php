<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\General\Model;

use AbdullahMateen\LaravelHelpingMaterial\Enums\StatusEnum;
use Illuminate\Support\Facades\Gate;

trait ScopeTrait
{
    public function scopeColumns($query, $columns = [], $overwrite = false)
    {
        $default = ['id', 'name'];
        $columns = is_array($columns) ? $columns : explode(',', $columns);
        $columns = $overwrite ? $columns : array_merge_recursive($default, $columns);
        return $query->select($columns);
    }

    public function scopeAuth($query, $columnName = 'user_id', $authId = null)
    {
        if (!auth_check()) return $query;
        $authId = $authId ?? auth_id();
        return $query->where($columnName, '=', $authId);
    }

    public function scopeWhereDateBetween($query, string $column, string|array|null $fromDate, string|null $toDate = null)
    {
        $dateRange = is_array($fromDate) ? array_values($fromDate) : [$fromDate, $toDate];

        //        $dateRange = array_filter($dateRange);
        //        if (count($dateRange) < 1) return $query;

        $start = $dateRange[0] ?? $dateRange[1] ?? null;
        $end = $dateRange[1] ?? $dateRange[0] ?? null;
        return $query->whereDate($column, '>=', $start)->whereDate($column, '<=', $end);
    }

    public function scopeActive($query)
    {
        return $query->where(get_model_table(get_called_class()).'.status', '=', StatusEnum::Active);
    }

    public function scopeInActive($query)
    {
        return $query->where(get_model_table(get_called_class()).'.status', '=', StatusEnum::Inactive);
    }
}
