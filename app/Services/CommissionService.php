<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Wallet;
use App\Models\DoctorProfile;
use App\Models\AgentProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    /**
     * Calculate and apply commissions for an invoice.
     * This credits the wallets of the referring doctor, agent, and collection center.
     */
    public function applyCommissions(Invoice $invoice)
    {
        // 1. Doctor Commission
        if ($invoice->referred_by_doctor_id && $invoice->doctor_commission_amount > 0) {
            $doctorWallet = Wallet::firstOrCreate(
                ['user_id' => $invoice->referred_by_doctor_id, 'company_id' => $invoice->company_id],
                ['balance' => 0]
            );
            $doctorWallet->credit(
                $invoice->doctor_commission_amount,
                "Commission for Invoice Rank #" . $invoice->invoice_number,
                'Invoice',
                $invoice->id
            );
        }

        // 2. Agent Commission
        if ($invoice->referred_by_agent_id && $invoice->agent_commission_amount > 0) {
            $agentWallet = Wallet::firstOrCreate(
                ['user_id' => $invoice->referred_by_agent_id, 'company_id' => $invoice->company_id],
                ['balance' => 0]
            );
            $agentWallet->credit(
                $invoice->agent_commission_amount,
                "Commission for Invoice Rank #" . $invoice->invoice_number,
                'Invoice',
                $invoice->id
            );
        }

        // 3. Collection Center Profit (B2C - B2B price difference)
        if ($invoice->collection_center_id && $invoice->cc_profit_amount > 0) {
            // Find the CC user ID linked to this collection center
            $ccUser = \App\Models\User::where('collection_center_id', $invoice->collection_center_id)
                ->where('company_id', $invoice->company_id)
                ->first();

            if ($ccUser) {
                $ccWallet = Wallet::firstOrCreate(
                    ['user_id' => $ccUser->id, 'company_id' => $invoice->company_id],
                    ['balance' => 0]
                );
                $ccWallet->credit(
                    $invoice->cc_profit_amount,
                    "Profit Margin for Invoice #" . $invoice->invoice_number,
                    'Invoice',
                    $invoice->id
                );
            }
        }
    }

    /**
     * Reverse commissions for a given invoice.
     * This debits the wallets of the referring doctor, agent, and collection center.
     */
    public function reverseCommissions(Invoice $invoice, string $reason = "Invoice Cancelled")
    {
        // 1. Reverse Doctor Commission
        if ($invoice->referred_by_doctor_id && $invoice->doctor_commission_amount > 0) {
            $doctorWallet = Wallet::where('user_id', $invoice->referred_by_doctor_id)
                ->where('company_id', $invoice->company_id)
                ->first();

            if ($doctorWallet) {
                $doctorWallet->debit(
                    $invoice->doctor_commission_amount,
                    "Reversal: {$reason} #" . $invoice->invoice_number,
                    'Invoice_Cancel',
                    $invoice->id
                );
            }
        }

        // 2. Reverse Agent Commission
        if ($invoice->referred_by_agent_id && $invoice->agent_commission_amount > 0) {
            $agentWallet = Wallet::where('user_id', $invoice->referred_by_agent_id)
                ->where('company_id', $invoice->company_id)
                ->first();

            if ($agentWallet) {
                $agentWallet->debit(
                    $invoice->agent_commission_amount,
                    "Reversal: {$reason} #" . $invoice->invoice_number,
                    'Invoice_Cancel',
                    $invoice->id
                );
            }
        }

        // 3. Reverse CC Profit
        if ($invoice->collection_center_id && $invoice->cc_profit_amount > 0) {
            $ccUser = \App\Models\User::where('collection_center_id', $invoice->collection_center_id)
                ->where('company_id', $invoice->company_id)
                ->first();

            if ($ccUser) {
                $ccWallet = Wallet::where('user_id', $ccUser->id)
                    ->where('company_id', $invoice->company_id)
                    ->first();

                if ($ccWallet) {
                    $ccWallet->debit(
                        $invoice->cc_profit_amount,
                        "Reversal: {$reason} #" . $invoice->invoice_number,
                        'Invoice_Cancel',
                        $invoice->id
                    );
                }
            }
        }
    }
}
