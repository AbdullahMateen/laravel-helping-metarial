<?php declare(strict_types = 1);

namespace Tests\Feature;

use AbdullahMateen\LaravelHelpingMaterial\Enums\StatusEnum;
use AbdullahMateen\LaravelHelpingMaterial\Enums\User\AccountStatusEnum;
use PHPUnit\Framework\TestCase;

final class EnumTest extends TestCase
{
    public function test_enum()
    {
        dd([
            'AccountStatusEnum'  => AccountStatusEnum::editable(),

            "asArray"            => StatusEnum::asArray(),
            "cases"              => StatusEnum::cases(),
            "exists1"            => StatusEnum::exists(1),
            "random1"            => StatusEnum::random(),
            "arrayable"          => StatusEnum::toArray(),
            "toFullArray"        => StatusEnum::toFullArray(),
            "toFullArrayInclude" => StatusEnum::toFullArrayInclude(),
            "toString"           => StatusEnum::tryFrom(1)->toString(),
            "color"              => StatusEnum::tryFrom(1)->color(),
            "colorCode"          => StatusEnum::tryFrom(1)->colorCode(),
            "value"              => StatusEnum::tryFrom(1)->value,
            "name"               => StatusEnum::tryFrom(1)->name,
        ]);
    }
}
