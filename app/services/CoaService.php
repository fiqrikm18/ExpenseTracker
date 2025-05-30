<?php

namespace App\services;

use App\DTOs\Coa\CoaDto;
use App\DTOs\Datatables\DatatableFilteringDto;
use App\Models\Coa;
use App\repositories\CoaRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yajra\DataTables\Facades\DataTables;

class CoaService
{
    private $coaRepository;

    public function __construct(CoaRepository $coaRepository)
    {
        $this->coaRepository = $coaRepository;
    }

    public function getAllCoa(Request $request)
    {
        if ($request->source == 'dropdown') {
            return $this->coaRepository->getAllCoa();
        }

        $datableFilteringDto = DatatableFilteringDto::fromArray($request->all());
        $coaObj = $this->coaRepository->getCoaBuilder($datableFilteringDto);
        return DataTables::eloquent($coaObj)
            ->addIndexColumn()
            ->addColumn('action', function ($coa) {
                return view('partials._action', [
                    'data' => $coa,
                    'edit_btn_id' => 'editCoa',
                    'delete_btn_id' => 'deleteCoa',
                ]);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getCoaById($id)
    {
        $coa = $this->coaRepository->findById($id);
        if (!$coa) {
            throw new NotFoundHttpException('Coa not found');
        }

        return $coa;
    }

    public function createCoa(Request $request)
    {
        $coaDto = CoaDto::fromArray($request->all());
        $this->coaRepository->create($coaDto);
    }

    public function deleteCoa(int $id): void
    {
        $this->coaRepository->delete($id);
    }

    public function updateCoa(int $id, Request $request): void
    {
        $coa = $this->coaRepository->findById($id);
        if (!$coa) {
            throw new NotFoundHttpException('Coa not found');
        }

        $coaDto = CoaDto::fromArray($request->all());
        $this->coaRepository->update($coaDto, $id);
    }

}
