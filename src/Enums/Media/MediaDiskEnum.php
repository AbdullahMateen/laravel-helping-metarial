<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Enums\Media;

use AbdullahMateen\LaravelHelpingMaterial\Interfaces\ColorsInterface;
use AbdullahMateen\LaravelHelpingMaterial\Traits\General\Enum\GeneralTrait;

enum MediaDiskEnum: int implements ColorsInterface
{
    use GeneralTrait;

    case Temp = 1;
    case Project = 2;
    case Placeholders = 3;

    /**
     * @return string
     */
    public function disk(): string
    {
        return strtolower($this->name);
    }

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
