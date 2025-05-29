<?php

namespace App\DTOs\CoaCategory;

use App\DTOs\DTO;

class CoaCategoryDto extends DTO
{

    public string $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromArray(array $data): CoaCategoryDto
    {
        return new self($data['name']);
    }
}
