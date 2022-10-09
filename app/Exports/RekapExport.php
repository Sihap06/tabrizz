<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;

class RekapExport extends DefaultValueBinder implements FromView, WithCustomValueBinder, WithEvents
{
    public function __construct($data, $tgl_awal, $tgl_akhir)
    {
        $this->data = $data;
        $this->tgl_akhir = $tgl_akhir;
        $this->tgl_awal = $tgl_awal;
    }
    use Exportable;

    public function view(): View
    {
        return view('admin.rekap_pdf', [
            'data' => $this->data,
            'tgl_awal' => $this->tgl_awal,
            'tgl_akhir' => $this->tgl_akhir,
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
