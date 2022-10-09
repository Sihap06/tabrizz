<?php

namespace App\Exports;

use App\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

// use Maatwebsite\Excel\Concerns\FromCollection;

class RekapStockExport extends DefaultValueBinder implements FromView, WithCustomValueBinder, WithEvents
{
    public function __construct($data)
    {
        $this->data = $data;
    }
    use Exportable;

    public function view(): View
    {
        return view('admin.shop.rekap_stock', [
            'data' => $this->data,

        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                foreach (range('A', 'I') as $columnID) {
                    $event->sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            },
        ];
    }
}
