<div class="patient-info-box">
    <table class="patient-info-table">
        <tr>
            <td colspan="2"><span class="label">Name</span></td>
            <td colspan="3"><span class="value">: {{ $patient['name'] }}</span></td>
            <td colspan="2"><span class="label">Report ID</span></td>
            <td colspan="3"><span class="value">: {{ $patient['report_id'] }}</span></td>
            <!-- NEW COLUMN -->
            <td rowspan="4" colspan="2" style="text-align:center; vertical-align:middle;">
                <img src="{{ public_path('assets/images/qr-code.png') }}" width="60px" height="60px" alt="">
            </td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Age/Gender</span></td>
            <td colspan="3"><span class="value">: {{ $patient['age'] }} / {{ $patient['gender'] }}</span></td>
            <td colspan="2"><span class="label">Collection Date</span></td>
            <td colspan="3"><span class="value">: {{ $patient['collection_date'] }}</span></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Referred By</span></td>
            <td colspan="3"><span class="value">: {{ $patient['referred_by'] }}</span></td>
            <td colspan="2"><span class="label">Report Date</span></td>
            <td colspan="3"><span class="value">: {{ $patient['report_date'] }}</span></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Patient ID</span></td>
            <td colspan="3"><span class="value">: {{ $patient['patient_id'] }}</span></td>
            <td colspan="2"></td>
            <td colspan="3"></td>
        </tr>
    </table>
</div>
