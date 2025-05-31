<?php

namespace App\services;

use App\DTOs\Datatables\DatatableFilteringDto;
use App\DTOs\Transaction\TransactionDto;
use App\repositories\CoaRepository;
use App\repositories\TransactionRepository;
use App\Traits\Formatter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yajra\DataTables\Facades\DataTables;

class TransactionService
{
    use Formatter;

    private $transactionRepository;
    private $coaRepository;

    public function __construct(TransactionRepository $transactionRepository, CoaRepository $coaRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->coaRepository = $coaRepository;
    }

    public function createTransaction(Request $request)
    {
        $transactionDto = TransactionDto::fromArray($request->all());
        if ($transactionDto->type == 'debit') {
            $coa = $this->coaRepository->findById($transactionDto->coaId);
            $balance = $this->transactionRepository->getBalance($coa->coa_category_id);
            $expense = $this->transactionRepository->getExpense($coa->coa_category_id);

            $balanceAmount = $balance ? $balance->total_amount : 0;
            $expenseAmount = $expense ? $expense->total_amount : 0;

            $actualBalance = $balanceAmount - $expenseAmount;
            if ($actualBalance < $transactionDto->amount) {
                throw new BadRequestHttpException('Insufficient balance');
            }
        }

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
        $period = isset($request->period) ? $request->period : null;
        $datableFilteringDto = DatatableFilteringDto::fromArray($request->all());
        $coaObj = $this->transactionRepository->getTransactionBuilder($datableFilteringDto, $period);

        return DataTables::eloquent($coaObj)
            ->addIndexColumn()
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('amount', function ($row) {
                if ($row->type == 'credit') {
                    return $this->rupiah($row->amount);
                }

                return $this->rupiah($row->amount);
            })
            ->addColumn('type', function ($row) {
                return ucwords($row->type);
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

    public function getTransactionReportSummary(Request $request)
    {
        $period = isset($request->period) ? $request->period : 30;
        $transactionCount = $this->transactionRepository->getTotalTransactions($period);
        $transactionIncome = $this->transactionRepository->getTotalIncome($period);
        $transactionExpense = $this->transactionRepository->getTotalExpense($period);

        return [
            'transactionCount' => $transactionCount,
            'transactionIncome' => $this->rupiah($transactionIncome),
            'transactionExpense' => $this->rupiah($transactionExpense),
        ];
    }

    public function getTransactionReportChart(Request $request)
    {
        $period = isset($request->period) ? $request->period : 30;
        return $this->transactionRepository->getTransactionChart($request->type, $period);
    }

    public function getTransactionReportMonthly(Request $request)
    {
        $income = $this->transactionRepository->getTransactionReportMonthly($request, 'income');
        $expense = $this->transactionRepository->getTransactionReportMonthly($request, 'expense');

        $incomeCoaCategory = collect($income)->pluck('coa_category_name')->unique()->values()->toArray();
        $mappedIncome = [];
        $mappedExpense = [];
        foreach ($incomeCoaCategory as $coaCategory) {
            $tempIncomeData = [
                'coa_category_name' => $coaCategory,
                'amount_data' => []
            ];

            $tempExpenseData = [
                'coa_category_name' => $coaCategory,
                'amount_data' => []
            ];

            $filteredItem = collect($income)->filter(function ($item) use ($coaCategory) {
                return $item->coa_category_name == $coaCategory;
            })->values()->toArray();

            foreach ($filteredItem as $item) {
                $tempIncomeData['amount_data'][] = [
                    'amount' => $item['total_amount'],
                    'month' => $item['month'],
                    'year' => $item['year'],
                ];
            }

            $mappedIncome[] = $tempIncomeData;

            $filteredItem = collect($expense)->filter(function ($item) use ($coaCategory) {
                return $item->coa_category_name == $coaCategory;
            })->values()->toArray();

            foreach ($filteredItem as $item) {
                $tempExpenseData['amount_data'][] = [
                    'amount' => $item['total_amount'],
                    'month' => $item['month'],
                    'year' => $item['year'],
                ];
            }

            $mappedExpense[] = $tempExpenseData;
        }

        return [
            'income' => $mappedIncome,
            'expense' => $mappedExpense,
        ];
    }

}
