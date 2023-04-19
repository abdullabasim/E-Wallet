<?php
namespace App\ServicesData\ExcelExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;


class ExportExcelService  implements FromArray, WithHeadings,WithStrictNullComparison
{


    use Exportable;

    private $data;
    private $titles;

    public function __construct($data,$titles)
    {
        $this->data = $data;
        $this->titles = $titles;
    }
    public function array(): array
    {
       return  $this->data;
    }


    public function headings(): array
    {
        return $this->titles;

    }



}
