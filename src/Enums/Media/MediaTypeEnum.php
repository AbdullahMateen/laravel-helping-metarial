<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Enums\Media;

use AbdullahMateen\LaravelHelpingMaterial\Interfaces\ColorsCodeInterface;
use AbdullahMateen\LaravelHelpingMaterial\Traits\General\Enum\GeneralTrait;

enum MediaTypeEnum: int implements ColorsCodeInterface
{
    use GeneralTrait;

    case Image = 1;
    case Audio = 2;
    case Video = 3;
    case Document = 4;
    case Archive = 5;

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->name;
        // return match ($this) {
        //     self::Image    => 'Image',
        //     self::Video    => 'Video',
        //     self::Document => 'Document',
        //     self::Archive  => 'Archive',
        // };
    }

}
