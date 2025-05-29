<?php

namespace App\DTOs;

abstract class DTO
{

    abstract static function fromArray(array $array);

}
