<?php

namespace App\DTOs\Datatables;

use App\DTOs\DTO;

class DatatableFilteringDto extends DTO
{

    public int $draw;
    public array $column;
    public array $order;
    public int $start;
    public int $length;
    public array $search;

    /**
     * @param int $draw
     * @param array $column
     * @param array $order
     * @param int $start
     * @param int $length
     * @param array $search
     */
    public function __construct(int $draw, array $column, array $order, int $start, int $length, array $search)
    {
        $this->draw = $draw;
        $this->column = $column;
        $this->order = $order;
        $this->start = $start;
        $this->length = $length;
        $this->search = $search;
    }

    static function fromArray(array $array): DatatableFilteringDto
    {
        return new self(
            $array['draw'],
            $array['columns'],
            isset($array['order']) ? [
                'colName' => $array['columns'][$array['order'][0]['column']]['data'],
                'order' => $array['order'][0]['dir'],
            ] : [
                'colName' => 'created_at',
                'order' => 'desc',
            ],
            $array['start'],
            $array['length'],
            $array['search']
        );
    }
}
