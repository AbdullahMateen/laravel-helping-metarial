<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Enums\Media;

use AbdullahMateen\LaravelHelpingMaterial\Interfaces\ColorsCodeInterface;
use AbdullahMateen\LaravelHelpingMaterial\Traits\General\Enum\GeneralTrait;

enum MediaDiskEnum: int implements ColorsCodeInterface
{
    use GeneralTrait;

    case Temp = 1;
    case Project = 2;
    case Placeholders = 3;
    case Archive = 4;

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->name;
        // return match ($this) {
        //     self::Temp         => 'Temp',
        //     self::Project      => 'Project',
        //     self::Placeholders => 'Placeholders',
        //     self::Archive      => 'Archive',
        // };
    }

}
