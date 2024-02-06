<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Enums;

use AbdullahMateen\LaravelHelpingMaterial\Interfaces\ColorsCodeInterface;
use AbdullahMateen\LaravelHelpingMaterial\Traits\General\Enum\GeneralTrait;

enum StatusEnum: int implements ColorsCodeInterface
{
    use GeneralTrait;

    case Active = 1;
    case InActive = 0;

    /**
     * @return string
     */
    public function toString(): string
    {
        return match ($this) {
            self::Active   => 'Active',
            self::InActive => 'In-Active',
        };
    }

    /**
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            self::Active   => self::SUCCESS_CLASS,
            self::InActive => self::DANGER_CLASS,
        };
    }

}
