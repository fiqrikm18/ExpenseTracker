<?php

namespace App\services;

use App\DTOs\CoaCategory\CoaCategoryDto;
use App\DTOs\Datatables\DatatableFilteringDto;
use App\Models\CoaCategory;
use App\repositories\CoaCategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    public function getAllCoaCategories(Request $request): \Illuminate\Http\JsonResponse | \Illuminate\Database\Eloquent\Collection
    {
        if ($request->source == 'dropdown') {
            return $this->coaCategoryRepository->getCoaCategories();
        } else {
            $datableFilteringDto = DatatableFilteringDto::fromArray($request->all());
            $coaCatObj = $this->coaCategoryRepository->getCoaCategoriesBuilder($datableFilteringDto);
            return DataTables::eloquent($coaCatObj)
                ->addIndexColumn()
                ->addColumn('action', function ($coaCat) {
                    return view('partials._action', [
                        'data' => $coaCat,
                        'edit_btn_id' => 'editCoaCategory',
                        'delete_btn_id' => 'deleteCoaCategory',
                    ]);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function getCoaCategoryById(int $id)
    {
        $coaCategory = $this->coaCategoryRepository->getCoaCategoryById($id);
        if (!$coaCategory) {
            throw new NotFoundHttpException('Coa Category not found.');
        }

        return $coaCategory;
    }

    /**
     * @throws \Exception
     */
    public function updateCoaCategory(Request $request, int $id)
    {
        $coaCategoryExist = $this->coaCategoryRepository->getCoaCategoryById($id);
        if (!$coaCategoryExist) {
            throw new NotFoundHttpException('Coa Category not found.');
        }

        $coaCategoryDto = CoaCategoryDto::fromArray($request->all());
        $this->coaCategoryRepository->update($coaCategoryDto, $id);
    }

}
