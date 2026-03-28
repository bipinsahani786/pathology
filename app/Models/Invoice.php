<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use BelongsToCompany;
    // Allow all fields to be mass-assigned safely
    protected $guarded = []; 
    
    protected $casts = [
        'invoice_date' => 'datetime',
        'expected_report_time' => 'datetime',
        'sample_received_at' => 'datetime',
        'sample_collected_at' => 'datetime',
    ];

    public function collectionCenter() 
    {
        return $this->belongsTo(CollectionCenter::class);
    }

    /**
     * The processing branch (Lab) for this invoice.
     */
    public function branch() 
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * The patient (User) who this invoice belongs to.
     */
    public function patient() 
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * The staff member (User) who created this invoice.
     */
    public function creator() 
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The individual lab tests or packages included in this invoice.
     */
    public function items() 
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * The payment history/logs associated with this invoice.
     */
    public function payments() 
    {
        return $this->hasMany(Payment::class);
    }
    
    /**
     * The doctor who referred the patient for these tests (for commission tracking).
     */
    public function doctor() 
    {
        return $this->belongsTo(User::class, 'referred_by_doctor_id');
    }

    /**
     * The agent who referred the patient for these tests.
     */
    public function agent() 
    {
        return $this->belongsTo(User::class, 'referred_by_agent_id');
    }

    /**
     * The generated test report for this invoice.
     */
    public function testReport()
    {
        return $this->hasOne(TestReport::class);
    }

    /**
     * Settlement relationships
     */
    public function doctorSettlement() { return $this->belongsTo(Settlement::class, 'doctor_settlement_id'); }
    public function agentSettlement() { return $this->belongsTo(Settlement::class, 'agent_settlement_id'); }
    public function ccSettlement() { return $this->belongsTo(Settlement::class, 'cc_settlement_id'); }

    /**
     * Cancel the invoice and reverse any commissions credited to wallets.
     */
    public function cancel()
    {
        if ($this->status === 'Cancelled') {
            return ['status' => true, 'message' => 'Already cancelled.'];
        }

        // Prevent cancellation if report is being processed or is ready
        if (in_array($this->sample_status, ['Processing', 'Ready'])) {
            return ['status' => false, 'message' => "Cannot cancel invoice when report is in '{$this->sample_status}' status."];
        }

        \DB::transaction(function () {
            // 1. Reverse Doctor Commission
            if ($this->referred_by_doctor_id && $this->doctor_commission_amount > 0) {
                $wallet = Wallet::where('user_id', $this->referred_by_doctor_id)->first();
                if ($wallet) {
                    $wallet->debit(
                        $this->doctor_commission_amount,
                        "Commission Reversal (Invoice Cancelled) #{$this->invoice_number}",
                        'invoice',
                        $this->id
                    );
                }
            }

            // 2. Reverse Agent Commission
            if ($this->referred_by_agent_id && $this->agent_commission_amount > 0) {
                $wallet = Wallet::where('user_id', $this->referred_by_agent_id)->first();
                if ($wallet) {
                    $wallet->debit(
                        $this->agent_commission_amount,
                        "Commission Reversal (Invoice Cancelled) #{$this->invoice_number}",
                        'invoice',
                        $this->id
                    );
                }
            }

            // 3. Update Invoice Status
            $this->update([
                'status' => 'Cancelled',
                'payment_status' => 'Unpaid', // Reset payment status if cancelled? 
                'doctor_commission_amount' => 0,
                'agent_commission_amount' => 0,
            ]);
        });

        return ['status' => true, 'message' => "Invoice #{$this->invoice_number} cancelled successfully."];
    }
}
