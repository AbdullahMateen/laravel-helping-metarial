<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\General\Enum;

trait GeneralTrait8
{
    public static function exists($value, $type = 'value')
    {
        if (is_null($value)) return false;
        foreach (self::cases() as $item) if ($item->$type == $value) return true;
        return  false;
    }

    public static function random($type = 'value')
    {
        return self::cases()[array_rand(self::cases())]->$type;
    }

    public static function toArray($useToStringValue = false)
    {
        $array = [];
        foreach (self::cases() as $item) $array[$useToStringValue ? $item->toString() : $item->name] = $item->value;
        return $array;
    }

    public function equalsTo($value, $type = 'value', $strict = false)
    {
        return $strict ? $this->$type === $value : $this->$type == $value;
    }

    public function toString() {
        return $this->name;
    }

    public function color() {
        return self::PRIMARY_CLASS;
    }
}
