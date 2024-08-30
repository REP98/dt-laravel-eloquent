<?php
namespace DTLaravelEloquent\Controllers;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataExportController implements FromCollection, WithHeadings
{
    /**
     * Datos del Excel
     *
     * @var array
     */
    protected $data;
    /**
     * Cabecera del Excel
     *
     * @var array
     */
    protected $headings;

    public function __construct($data, $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
