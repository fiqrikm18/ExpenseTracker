<?php

namespace App\Exports;

use App\Models\Transaction;
use App\services\TransactionService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use phpDocumentor\Reflection\Types\True_;

class TransactionDetailExport  implements FromView, WithTitle
{
    private $year;
    private $transactionService;

    public function __construct($year, TransactionService $transactionService)
    {
        $this->year = $year;
        $this->transactionService = $transactionService;
    }

    public function title(): string
    {
        return 'Transaction Detail '.  $this->year;
    }

    public function view(): View
    {
        return view('exports.transaction.details', [
            'transactions' => $this->getTransactionData()
        ]);
    }

    private function getTransactionData()
    {
        $transaction = Transaction::query();

        $transaction->join('coas', 'transactions.coa_id', '=', 'coas.id');
        $transaction->join('coa_categories', 'coas.coa_category_id', '=', 'coa_categories.id');
        $transaction->where(DB::raw('EXTRACT (YEAR FROM transactions.created_at)'), '=', $this->year);
        $transaction->selectRaw('transactions.id, transactions.created_at, transactions.amount, transactions.type,
            transactions.description, coas.name as coa_name, coa_categories.name as coa_category_name');

        return $transaction->get();
    }
}
