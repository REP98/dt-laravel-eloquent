<?php
namespace DTLaravelEloquent\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use DTLaravelEloquent\Controllers\DataExportController;
use Illuminate\Http\Request;

class RDataTableController
{
    /**
     * Exporta un excel
     *
     * @param   Request  $request  datos a exportar
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse

     */
    public function exportExcel(Request $request)
    {
        $jsonData = $request->input('data');
        $headings = $request->input('headings');
        // Lógica para exportar a Excel
        return Excel::download(
            new DataExportController($jsonData, $headings), 
            config('RDataTable.export.name', 'RDTExcel').'.xlsx'
        );
    }
    /**
     * Genera y exporta un PDF
     *
     * @param   Request  $request  Datos del PDF
     *
     * @return  \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        $jsonData = $request->input('data');
        $headings = $request->input('headings');

        $data = array_map(function($row) use ($headings) {
            return array_combine($headings, $row);
        }, $jsonData);

        // Lógica para exportar a PDF
        $pdf = Pdf::loadView('datatable::pdf', ['data' => $data]);
        return $pdf->download(config('RDataTable.export.name', 'RDTPDF').'.pdf');
    }

}