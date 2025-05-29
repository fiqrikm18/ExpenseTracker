<?php

namespace App\services;

use App\DTOs\CoaCategory\CoaCategoryDto;
use App\DTOs\Datatables\DatatableFilteringDto;
use App\Models\CoaCategory;
use App\repositories\CoaCategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CoaCategoryService
{

    private $coaCategoryRepository;

    public function __construct(CoaCategoryRepository $coaCategoryRepository)
    {
        $this->coaCategoryRepository = $coaCategoryRepository;
    }

    public function createCoaCategory(Request $request)
    {
        $coaCategoryDto = CoaCategoryDto::fromArray($request->all());
        $this->coaCategoryRepository->create($coaCategoryDto);
    }

    public function deleteCoaCategory($id)
    {
        $this->coaCategoryRepository->delete($id);
    }

    /**
     * @throws \Exception
     */
    public function getAllCoaCategories(Request $request): \Illuminate\Http\JsonResponse
    {
        $datableFilteringDto = DatatableFilteringDto::fromArray($request->all());
        $coaCatObj = $this->coaCategoryRepository->getCoaCategoriesBuilder($datableFilteringDto);
        return DataTables::eloquent($coaCatObj)
            ->addIndexColumn()
            ->addColumn('action', function ($coaCat) {
                return view('partials._action', [
                    'coaCat' => $coaCat,
                    'edit_btn_id' => 'editCoaCategory',
                    'delete_btn_id' => 'deleteCoaCategory',
                ]);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getCoaCategoryById(int $id)
    {
        return $this->coaCategoryRepository->getCoaCategoryById($id);
    }

    /**
     * @throws \Exception
     */
    public function updateCoaCategory(Request $request, int $id)
    {
        $coaCategoryExist = $this->coaCategoryRepository->getCoaCategoryById($id);
        if (!$coaCategoryExist) {
            throw new \Exception('Coa Category Not Found', JsonResponse::HTTP_NOT_FOUND);
        }

        $coaCategoryDto = CoaCategoryDto::fromArray($request->all());
        $this->coaCategoryRepository->update($coaCategoryDto, $id);
    }

}
