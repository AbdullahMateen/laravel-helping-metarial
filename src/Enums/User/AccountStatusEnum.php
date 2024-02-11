<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Enums\User;

use AbdullahMateen\LaravelHelpingMaterial\Interfaces\ColorsCodeInterface;
use AbdullahMateen\LaravelHelpingMaterial\Traits\General\Enum\GeneralTrait;

enum AccountStatusEnum: int implements ColorsCodeInterface
{
    use GeneralTrait;

    case Pending = 0;
    case Unverified = 1;
    case Active = 2;
    case Inactive = 3;
    case Suspend = 4;
    case Blocked = 5;

    /**
     * @return AccountStatusEnum[]
     */
    public static function simple(): array
    {
        return [
            self::Active,
            self::Blocked,
        ];
    }

    /**
     * @return AccountStatusEnum[]
     */
    public static function editable(): array
    {
        return [
            self::Active,
            self::Inactive,
            self::Suspend,
            self::Blocked,
        ];
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->name;
        // return match ($this) {
        //     self::Pending    => 'Pending',
        //     self::Unverified => 'Unverified',
        //     self::Active     => 'Active',
        //     self::Inactive   => 'Inactive',
        //     self::Suspend    => 'Suspend',
        //     self::Blocked    => 'Blocked',
        // };
    }

    /**
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending, self::Unverified => self::PRIMARY_CLASS,
            self::Active                    => self::SUCCESS_CLASS,
            self::Inactive, self::Suspend   => self::WARNING_CLASS,
            self::Blocked                   => self::DANGER_CLASS,
        };
    }

}