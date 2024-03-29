<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Enums\User;

use AbdullahMateen\LaravelHelpingMaterial\Interfaces\ColorsInterface;
use AbdullahMateen\LaravelHelpingMaterial\Traits\General\Enum\GeneralTrait;

enum GenderEnum: string implements ColorsInterface
{
    use GeneralTrait;

    case Male = 'm';
    case Female = 'f';
    case Other = 'o';

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->name;
        // return match ($this) {
        //     self::Male   => 'Male',
        //     self::Female => 'Female',
        //     self::Other  => 'Other',
        // };
    }

    /**
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            self::Male   => self::DEEPSKYBLUE_CLASS,
            self::Female => self::HOTPINK_CLASS,
            self::Other  => self::SECONDARY_CLASS,
        };
    }

}
