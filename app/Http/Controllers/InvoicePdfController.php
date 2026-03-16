<?php

namespace App\Http\Controllers;

use App\Models\{Invoice, Company, Configuration};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoicePdfController extends Controller
{
    public function download($id)
    {
        $companyId = auth()->user()->company_id;
        $invoice = Invoice::where('company_id', $companyId)
            ->with(['items', 'payments.paymentMode', 'patient.patientProfile', 'doctor.doctorProfile', 'collectionCenter', 'creator'])
            ->findOrFail($id);

        $company = Company::find($companyId);
        $template = Configuration::getFor('bill_template', 'classic');
        $showHeader = Configuration::getFor('pdf_show_header', '1') === '1';
        $showFooter = Configuration::getFor('pdf_show_footer', '1') === '1';
        $headerImage = Configuration::getFor('pdf_header_image', null);
        $footerImage = Configuration::getFor('pdf_footer_image', null);

        $view = 'pdf.invoice-' . $template;
        if (!view()->exists($view)) {
            $view = 'pdf.invoice-classic';
        }

        $pdf = Pdf::loadView($view, [
            'invoice' => $invoice,
            'company' => $company,
            'showHeader' => $showHeader,
            'showFooter' => $showFooter,
            'headerImage' => $headerImage,
            'footerImage' => $footerImage,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function downloadWithoutHeader($id)
    {
        $companyId = auth()->user()->company_id;
        $invoice = Invoice::where('company_id', $companyId)
            ->with(['items', 'payments.paymentMode', 'patient.patientProfile', 'doctor.doctorProfile', 'collectionCenter', 'creator'])
            ->findOrFail($id);

        $company = Company::find($companyId);
        $template = Configuration::getFor('bill_template', 'classic');

        $view = 'pdf.invoice-' . $template;
        if (!view()->exists($view)) {
            $view = 'pdf.invoice-classic';
        }

        $pdf = Pdf::loadView($view, [
            'invoice' => $invoice,
            'company' => $company,
            'showHeader' => false,
            'showFooter' => false,
            'headerImage' => null,
            'footerImage' => null,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Invoice-' . $invoice->invoice_number . '-NoHeader.pdf');
    }
}
