<?php

namespace App\DTOs\Transaction;

use App\DTOs\DTO;

class TransactionDto extends DTO
{

    public int $amount;
    public string $type;
    public string $description;
    public int $coaId;

    /**
     * @param int $amount
     * @param string $type
     * @param string $description
     * @param int $coaId
     */
    public function __construct(int $amount, string $type, string $description, int $coaId)
    {
        $this->amount = $amount;
        $this->type = $type;
        $this->description = $description;
        $this->coaId = $coaId;
    }


    static function fromArray(array $array): TransactionDto
    {
        return new self(
            $array['amount'],
            $array['type'],
            $array['description'],
            $array['coa']
        );
    }

}
