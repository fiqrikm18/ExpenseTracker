<?php

namespace App\Http\Controllers;

use App\services\TransactionService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $transactionSummary = $this->transactionService->getTransactionReportSummary($request);
        return view('admin.dashboard.index', [
            'transactionSummary' => $transactionSummary,
        ]);
    }

}
