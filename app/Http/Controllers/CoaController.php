<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCoaRequest;
use App\services\CoaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    private $coaService;

    public function __construct(CoaService $coaService)
    {
        $this->coaService = $coaService;
    }

    public function index()
    {
        return view('admin.coa.index');
    }

    public function coaList(Request $request)
    {
        if ($request->source == 'dropdown') {
            $coas = $this->coaService->getAllCoa($request);
            return $this->successResponse($coas, 'Coa retrieved successfully.');
        }
        return $this->coaService->getAllCoa($request);
    }

    public function getCoa($id)
    {
        $coa = $this->coaService->getCoaById($id);
        return $this->successResponse($coa, 'Coa retrieved successfully.');
    }

    public function createCoa(CreateCoaRequest $request)
    {
        $this->coaService->createCoa($request);
        return $this->successResponse([], 'Create Coa successfully.', JsonResponse::HTTP_CREATED);
    }

    public function deleteCoa(int $id)
    {
        $this->coaService->deleteCoa($id);
        return $this->successResponse([], 'Delete Coa successfully.', JsonResponse::HTTP_OK);
    }

    public function updateCoa(int $id, Request $request)
    {
        $this->coaService->updateCoa($id, $request);
        return $this->successResponse([], 'Update Coa successfully.', JsonResponse::HTTP_OK);
    }

}
