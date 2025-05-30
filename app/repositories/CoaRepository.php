<?php

namespace App\repositories;

use App\DTOs\Coa\CoaDto;
use App\DTOs\Datatables\DatatableFilteringDto;
use App\Models\Coa;
use App\Models\CoaCategory;
use Illuminate\Support\Facades\DB;

class CoaRepository
{

    public function create(CoaDto $dto): void
    {
        DB::transaction(function () use ($dto) {
            Coa::create([
                'name' => $dto->name,
                'code' => $dto->code,
                'coa_category_id' => $dto->coa_category_id,
            ]);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            Coa::where('id', $id)->delete();
        });
    }

    public function update(CoaDto $dto, int $id): void
    {
        DB::transaction(function () use ($dto, $id) {
            Coa::where('id', $id)->update([
                'name' => $dto->name,
                'code' => $dto->code,
                'coa_category_id' => $dto->coa_category_id,
            ]);
        });
    }


    public function getCoaBuilder(DatatableFilteringDto $dto): \Illuminate\Database\Eloquent\Builder
    {
        $coaQuery = Coa::query();

        $coaQuery->join('coa_categories', 'coa_categories.id', '=', 'coas.coa_category_id');
        if ($dto->order['colName'] == 'created_at') {
            $coaQuery->orderBy('coas.created_at', $dto->order['order']);
        }
        $coaQuery->skip($dto->start);
        $coaQuery->limit($dto->length);
        $coaQuery->selectRaw('coas.id, coas.name, coas.code, coa_categories.name as category');

        if (isset($dto->search) && $dto->search['value'] != '') {
            $coaQuery->where(function ($query) use ($dto) {
                $query->where(DB::raw('LOWER(coas.name)'), 'like', '%' . $dto->search['value'] . '%');
                $query->orWhere('code', 'like', '%' . $dto->search['value'] . '%');
            });
        }

        return $coaQuery;
    }

    public function findById($id)
    {
        return Coa::where('id', $id)->first();
    }

}
