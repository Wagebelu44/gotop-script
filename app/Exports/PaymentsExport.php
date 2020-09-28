<?php
namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentsExport implements FromCollection, WithHeadings, WithTitle
{
    use Exportable;

    protected $payments, $headings;

    public function __construct($payments, array $headings)
    {
        $this->payments = $payments;
        $this->headings = $headings;
    }

    public function collection()
    {
        return $this->payments;
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
        return 'Payments';
    }
}
