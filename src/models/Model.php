<?php

namespace src\models;

interface Model
{
    public static function entity(): string;

    public static function safe(): array;
}
