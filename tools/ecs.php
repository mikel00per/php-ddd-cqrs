<?php


use Shared\Infrastructure\CodeStyle\StyleEnum;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([__DIR__ . '/../src',]);

    $ecsConfig->sets([StyleEnum::DEFAULT]);
};