<?php

namespace App\repositories;

use App\DTOs\Datatables\DatatableFilteringDto;
use App\DTOs\Transaction\TransactionDto;
use App\Models\Coa;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{

    public function create(TransactionDto $dto)
    {
        DB::transaction(function () use ($dto) {
            Transaction::create([
                'coa_id' => $dto->coaId,
                'type' => $dto->type,
                'amount' => $dto->amount,
                'created_by' => 1,
                'description' => $dto->description,
            ]);
        });
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            Transaction::where('id', $id)->delete();
        });
    }

    public function update($id, TransactionDto $dto)
    {
        DB::transaction(function () use ($id, $dto) {
            Transaction::where('id', $id)->update([
                'coa_id' => $dto->coaId,
                'type' => $dto->type,
                'amount' => $dto->amount,
                'created_by' => 1,
                'description' => $dto->description,
            ]);
        });
    }

    public function getTransactionBuilder(DatatableFilteringDto $dto, $period = null): \Illuminate\Database\Eloquent\Builder
    {
        $transaction = Transaction::query();

        $transaction->join('coas', 'transactions.coa_id', '=', 'coas.id');
        $transaction->join('coa_categories', 'coas.coa_category_id', '=', 'coa_categories.id');
        $transaction->skip($dto->start);
        $transaction->limit($dto->length);
        $transaction->selectRaw('transactions.id, transactions.created_at, transactions.amount, transactions.type,  transactions.description, coas.name as coa_name, coa_categories.name as coa_category_name');

        if ($dto->order['colName'] == 'created_at') {
            $transaction->orderBy('transactions.created_at', $dto->order['order']);
        }

        if (isset($dto->search) && $dto->search['value'] != '') {
            $transaction->where(function ($query) use ($dto) {
                $query->where(DB::raw('LOWER(coas.name)'), 'like', '%' . $dto->search['value'] . '%');
                $query->orWhere('code', 'like', '%' . $dto->search['value'] . '%');
            });
        }

        return $transaction;
    }

    public function findById($id)
    {
        return Transaction::where('transactions.id', $id)
            ->join('coas', 'transactions.coa_id', '=', 'coas.id')
            ->join('coa_categories', 'coas.coa_category_id', '=', 'coa_categories.id')
            ->selectRaw('transactions.id, transactions.created_at, transactions.amount, transactions.type,
                transactions.description, coas.name as coa_name, coa_categories.name as coa_category_name,
                coas.id as coa_id')
            ->first();
    }

    public function getBalance($coaCatId)
    {
        $transactionQuery = Transaction::query();

        $transactionQuery->join('coas', 'transactions.coa_id', '=', 'coas.id');
        $transactionQuery->join('coa_categories', 'coas.coa_category_id', '=', 'coa_categories.id');
        $transactionQuery->where('type', '=', 'credit');
        $transactionQuery->where('coa_categories.id', '=', $coaCatId);
        $transactionQuery->selectRaw('sum(transactions.amount) as total_amount');
        $transactionQuery->groupBy('coa_categories.id');

        return $transactionQuery->first();
    }

    public function getExpense($coaCatId)
    {
        $transactionQuery = Transaction::query();

        $transactionQuery->join('coas', 'transactions.coa_id', '=', 'coas.id');
        $transactionQuery->join('coa_categories', 'coas.coa_category_id', '=', 'coa_categories.id');
        $transactionQuery->where('type', '=', 'debit');
        $transactionQuery->where('coa_categories.id', '=', $coaCatId);
        $transactionQuery->selectRaw('sum(transactions.amount) as total_amount');
        $transactionQuery->groupBy('coa_categories.id');

        return $transactionQuery->first();
    }

    public function getTotalTransactions($period = 30)
    {
        $today = Carbon::now()->endOfDay();
        $prevDay = Carbon::now()->subDays($period)->startOfDay();

        return Transaction::count();
    }

    public function getTotalIncome($period = 30)
    {
        $today = Carbon::now()->endOfDay();
        $prevDay = Carbon::now()->subDays($period)->startOfDay();

        return Transaction::where('type', '=', 'credit')
            ->sum('amount');
    }

    public function getTotalExpense($period = 30)
    {
        $today = Carbon::now()->endOfDay();
        $prevDay = Carbon::now()->subDays($period)->startOfDay();

        return Transaction::where('type', '=', 'debit')
            ->sum('amount');
    }

    public function getTransactionChart($type, $period = 30)
    {
        $today = Carbon::now()->endOfDay();
        $prevDay = Carbon::now()->subDays($period)->startOfDay();
        $transactionType = 'credit';
        if ($type == 'expense') {
            $transactionType = 'debit';
        }

        $transactionQuery = Transaction::query();

        $transactionQuery->join('coas', 'transactions.coa_id', '=', 'coas.id');
        $transactionQuery->join('coa_categories', 'coas.coa_category_id', '=', 'coa_categories.id');
        $transactionQuery->where('type', '=', $transactionType);
        $transactionQuery->selectRaw('coa_categories.name as coa_category_name, sum(transactions.amount) as total_amount');
        $transactionQuery->groupBy('coa_categories.id');

        return $transactionQuery->get();
    }

    public function getTransactionReportMonthly($request, $type)
    {
        $year = '2025';
        $transactionType = 'credit';

        if ($type == 'expense') {
            $transactionType = 'debit';
        }

        if (isset($request->year)) {
            $year = $request->year;
        }

        $transactionQuery = Transaction::query();

        $transactionQuery->join('coas', 'transactions.coa_id', '=', 'coas.id');
        $transactionQuery->join('coa_categories', 'coas.coa_category_id', '=', 'coa_categories.id');
        $transactionQuery->where('type', '=', $transactionType);
        $transactionQuery->where(DB::raw('EXTRACT (YEAR FROM transactions.created_at)'), '=', $year);
        $transactionQuery->selectRaw('coa_categories.name as coa_category_name, sum(transactions.amount) as total_amount,
            EXTRACT ( YEAR FROM transactions.created_at) AS year, EXTRACT ( MONTH FROM transactions.created_at) AS month');
        $transactionQuery->groupBy(['coa_categories.id', 'year', 'month']);
        $transactionQuery->orderBy('total_amount', 'DESC');

        return $transactionQuery->get();
    }

}
