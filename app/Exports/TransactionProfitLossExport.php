<?php

namespace App\Exports;

use App\services\TransactionService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class TransactionProfitLossExport implements FromView, WithTitle
{
    private $year;

    private $transactionService;

    public function __construct(string $year, TransactionService $transactionService)
    {
        $this->year = $year;
        $this->transactionService = $transactionService;
    }

    public function title(): string
    {
        return 'Profit & Loss '.  $this->year;
    }

    public function view(): View
    {
        $currentYear = $this->year;
        $currentMonth = Carbon::now()->month;
        $request = new Request([
            'year' => $currentYear,
        ]);

        return view('exports.transaction.profit_loss', [
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth,
            'transactionReport' => $this->transactionService->getTransactionReportMonthly($request)
        ]);
    }
}
