<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCoaCategoryRequest;
use App\services\CoaCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CoaCategoryController extends Controller
{
    private $coaCategoryService;

    public function __construct(CoaCategoryService $coaCategoryService)
    {
        $this->coaCategoryService = $coaCategoryService;
    }

    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('admin.coa_category.index');
    }

    /**
     * @throws \Exception
     */
    public function coaCategoryList(Request $request): JsonResponse
    {
        return $this->coaCategoryService->getAllCoaCategories($request);
    }

    public function coaCategoryDetails(int $id): JsonResponse
    {
        $coaCategory = $this->coaCategoryService->getCoaCategoryById($id);
        return $this->successResponse($coaCategory, 'Get Coa Category Detail Successfully', Response::HTTP_OK);
    }

    public function createCoaCategory(CreateCoaCategoryRequest $request): JsonResponse
    {
        $this->coaCategoryService->createCoaCategory($request);
        return $this->successResponse([], 'Coa Category Created Successfully', Response::HTTP_CREATED);
    }

    public function deleteCoaCategory($id)
    {
        $this->coaCategoryService->deleteCoaCategory($id);
        return $this->successResponse([], 'Coa Category Deleted Successfully', Response::HTTP_OK);
    }

    public function updateCoaCategory(Request $request, int $id): JsonResponse
    {
        $this->coaCategoryService->updateCoaCategory($request, $id);
        return $this->successResponse([], 'Coa Category Updated Successfully', Response::HTTP_OK);
    }
}
