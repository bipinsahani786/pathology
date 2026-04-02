<?php

namespace App\Http\Controllers;

use App\Models\PatientMembership;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MembershipCardController extends Controller
{
    public function print($id)
    {
        $companyId = auth()->user()->company_id;
        $membership = PatientMembership::with(['patient.patientProfile', 'membership'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        $company = Company::find($companyId);

        $pdf = Pdf::loadView('pdf.membership-card', [
            'membership' => $membership,
            'patient' => $membership->patient,
            'plan' => $membership->membership,
            'company' => $company,
        ]);

        // Set card-like size (Custom size roughly 86mm x 54mm)
        $pdf->setPaper([0, 0, 243.78, 153.07], 'portrait'); 

        return $pdf->stream('MembershipCard-' . $membership->id . '.pdf');
    }
}
