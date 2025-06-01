<?php

namespace App\Exports;

use App\services\TransactionService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TransactionExport implements WithMultipleSheets
{
    use Exportable;

    private $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new TransactionProfitLossExport(Carbon::now()->year, $this->transactionService);
        $sheets[] = new TransactionDetailExport(Carbon::now()->year, $this->transactionService);

        return $sheets;
    }
}
