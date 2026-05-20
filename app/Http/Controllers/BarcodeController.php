<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

class BarcodeController extends Controller
{
    public function printStickers($id)
    {
        $invoice = Invoice::with(['patient.patientProfile', 'items.labTest'])
            ->findOrFail($id);

        return view('lab.invoice.barcode-stickers', compact('invoice'));
    }
}
