<?php

namespace App\DTOs\Coa;

use App\DTOs\DTO;

class CoaDto extends DTO
{
    public string $name;
    public string $code;
    public int $coa_category_id;

    /**
     * @param string $name
     * @param string $code
     * @param int $coa_category_id
     */
    public function __construct(string $name, string $code, int $coa_category_id)
    {
        $this->name = $name;
        $this->code = $code;
        $this->coa_category_id = $coa_category_id;
    }

    static function fromArray(array $array): CoaDto
    {
        return new self(
            $array['name'],
            $array['code'],
            $array['category'],
        );
    }
}
