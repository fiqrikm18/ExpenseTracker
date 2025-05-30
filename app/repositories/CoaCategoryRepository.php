<?php

namespace App\repositories;

use App\DTOs\CoaCategory\CoaCategoryDto;
use App\DTOs\Datatables\DatatableFilteringDto;
use App\Models\CoaCategory;
use Illuminate\Support\Facades\DB;

class CoaCategoryRepository
{

    public function getCoaCategoriesBuilder(DatatableFilteringDto $dto): \Illuminate\Database\Eloquent\Builder
    {
        $coaCategoryQuery = CoaCategory::query();

        $coaCategoryQuery->orderBy($dto->order['colName'], $dto->order['order']);
        $coaCategoryQuery->skip($dto->start);
        $coaCategoryQuery->limit($dto->length);

        if (isset($dto->search) && $dto->search['value'] != '') {
            $coaCategoryQuery->where(function ($query) use ($dto) {
                $query->where(DB::raw('LOWER(name)'), 'like', '%' . $dto->search['value'] . '%');
            });
        }

        return $coaCategoryQuery;
    }

    public function getCoaCategories()
    {
        return CoaCategory::all();
    }

    public function getCoaCategoryById(int $id)
    {
        return CoaCategory::where('id', $id)->first();
    }

    public function create(CoaCategoryDto $dto): void
    {
        DB::transaction(function () use ($dto) {
            CoaCategory::create([
                'name' => $dto->name
            ]);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            CoaCategory::where('id', $id)->delete();
        });
    }

    public function update(CoaCategoryDto $dto, $id): void
    {
        DB::transaction(function () use ($dto, $id) {
            CoaCategory::where('id', '=', $id)->update([
                'name' => $dto->name
            ]);
        });
    }

}
