<?php

namespace App\repositories;

use App\DTOs\Datatables\DatatableFilteringDto;
use App\DTOs\Transaction\TransactionDto;
use App\Models\Coa;
use App\Models\Transaction;
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

    public function getTransactionBuilder(DatatableFilteringDto $dto): \Illuminate\Database\Eloquent\Builder
    {
        $transaction = Transaction::query();

        $transaction->join('coas', 'transactions.coa_id', '=', 'coas.id');
        $transaction->join('coa_categories', 'coas.coa_category_id', '=', 'coa_categories.id');
        if ($dto->order['colName'] == 'created_at') {
            $transaction->orderBy('transactions.created_at', $dto->order['order']);
        }
        $transaction->skip($dto->start);
        $transaction->limit($dto->length);
        $transaction->selectRaw('transactions.id, transactions.created_at, transactions.amount, transactions.type,  transactions.description, coas.name as coa_name, coa_categories.name as coa_category_name');

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


}
