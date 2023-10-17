<?php

namespace Siktec\Dmm\Model;

interface IBaseModel
{
    public function toArray(bool $external, bool $generated, bool $nested): array;

    public function fromArray(array $data, bool $external): bool;

    public function toJson(bool $external, bool $generated, bool $nested, bool $pretty): string;

    public function fromJson(string $data, bool $external): bool;

    public function isValid(bool $nested, bool $allow_null): bool;

    public function validation(): array;
}
