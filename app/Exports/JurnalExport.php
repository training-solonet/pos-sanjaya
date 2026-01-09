<?php

namespace App\Exports;

use App\Models\Jurnal;

class JurnalExport
{
    protected $tanggal;

    public function __construct($tanggal)
    {
        $this->tanggal = $tanggal;
    }

    public function export()
    {
        $jurnals = Jurnal::whereDate('tgl', $this->tanggal)
            ->orderBy('tgl', 'asc')
            ->get();

        return $jurnals;
    }

    public function getTanggal()
    {
        return $this->tanggal;
    }
}
