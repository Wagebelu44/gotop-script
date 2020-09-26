<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings, WithTitle
{
    use Exportable;

    protected $users, $headings;

    public function __construct($users, array $headings)
    {
        $this->users = $users;
        $this->headings = $headings;
    }

    public function collection()
    {
        return $this->users;
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
        return 'Users';
    }
}
