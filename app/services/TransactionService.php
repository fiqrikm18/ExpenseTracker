<?php

namespace App\services;

use App\DTOs\Datatables\DatatableFilteringDto;
use App\DTOs\Transaction\TransactionDto;
use App\Models\Transaction;
use App\repositories\TransactionRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yajra\DataTables\Facades\DataTables;

class TransactionService
{

    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function createTransaction(Request $request)
    {
        $transactionDto = TransactionDto::fromArray($request->all());
        $this->transactionRepository->create($transactionDto);
    }

    public function deleteTransaction($id)
    {
        $exist = $this->transactionRepository->findById($id);
        if (!$exist) {
            throw new NotFoundHttpException('Transaction not found');
        }

        $this->transactionRepository->delete($id);
    }

    public function updateTransaction(Request $request, $id)
    {
        $exist = $this->transactionRepository->findById($id);
        if (!$exist) {
            throw new NotFoundHttpException('Transaction not found');
        }

        $transactionDto = TransactionDto::fromArray($request->all());
        $this->transactionRepository->update($id, $transactionDto);
    }

    public function getAllTransactions(Request $request)
    {
        $datableFilteringDto = DatatableFilteringDto::fromArray($request->all());
        $coaObj = $this->transactionRepository->getTransactionBuilder($datableFilteringDto);
        return DataTables::eloquent($coaObj)
            ->addIndexColumn()
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('amount', function ($row) {
                if ($row->type == 'credit') {
                    return '+ ' . $row->amount;
                }

                return '- ' . $row->amount;
            })
            ->addColumn('action', function ($transaction) {
                return view('partials._action_with_detail', [
                    'data' => $transaction,
                    'show_btn_id' => 'showTransaction',
                    'edit_btn_id' => 'editTransaction',
                    'delete_btn_id' => 'deleteTransaction',
                ]);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getTransactionById($id)
    {
        return $this->transactionRepository->findById($id);
    }

}
