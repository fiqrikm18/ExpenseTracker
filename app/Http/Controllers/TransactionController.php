<?php

namespace App\Http\Controllers;

use App\Exports\TransactionExport;
use App\Http\Requests\CreateTransactionRequest;
use App\services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{

    private $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        return view('admin.transaction.index');
    }

    public function createTransaction(CreateTransactionRequest $request)
    {
        $this->transactionService->createTransaction($request);
        return $this->successResponse([], 'Transaction created successfully.', JsonResponse::HTTP_CREATED);
    }

    public function deleteTransaction($id)
    {
        $this->transactionService->deleteTransaction($id);
        return $this->successResponse([], 'Transaction deleted successfully.', JsonResponse::HTTP_OK);
    }

    public function updateTransaction(Request $request, $id)
    {
        $this->transactionService->updateTransaction($request, $id);
        return $this->successResponse([], 'Transaction updated successfully.', JsonResponse::HTTP_OK);
    }

    public function transactionList(Request $request)
    {
        return $this->transactionService->getAllTransactions($request);
    }

    public function transactionDetail($id)
    {
        $transaction = $this->transactionService->getTransactionById($id);
        return $this->successResponse($transaction, 'Transaction retrieved successfully.');
    }

    public function transactionChart(Request $request)
    {
        $chartData = $this->transactionService->getTransactionReportChart($request);
        return $this->successResponse($chartData, 'Transaction retrieved successfully.');
    }

    public function transactionReport(Request $request)
    {
        $report = $this->transactionService->getTransactionReportMonthly($request);
        return $this->successResponse($report, 'Transaction retrieved successfully.');
    }

    public function exportTransaction(Request $request)
    {
        return Excel::download(new TransactionExport($this->transactionService), 'transactions.xlsx');
    }
}
