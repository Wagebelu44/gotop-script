<?php
namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings, WithTitle
{
    use Exportable;

    protected $orders, $headings;

    public function __construct($orders, array $headings)
    {
        $this->orders = $orders;
        $this->headings = $headings;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Orders';
    }
}
