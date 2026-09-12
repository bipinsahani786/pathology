<?php

namespace App\Services;

use App\Models\Configuration;
use App\Models\HomeCollection;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify patient when phlebotomist is assigned.
     */
    public function notifyPatientAssigned(HomeCollection $visit): void
    {
        $companyId = $visit->company_id;
        $template  = Configuration::getFor(
            'hc_whatsapp_template_assign',
            'Namaste {patient_name}! Aapka home sample collection {date} ko {slot} ke liye confirm ho gaya hai. Phlebotomist: {phlebotomist_name} ({phlebotomist_phone}). Dhanyawad!',
            $companyId
        );
        $message = $this->buildMessage($template, $visit);
        $phone   = $visit->invoice->patient->phone ?? null;

        if ($phone) {
            if (Configuration::getFor('hc_notify_whatsapp', '0', $companyId) === '1') {
                $this->sendWhatsApp($phone, $message, $companyId);
            }
            if (Configuration::getFor('hc_notify_sms', '0', $companyId) === '1') {
                $this->sendSms($phone, $message, $companyId);
            }
        }
    }

    /**
     * Notify patient when phlebotomist is en route.
     */
    public function notifyPatientEnRoute(HomeCollection $visit): void
    {
        $companyId = $visit->company_id;
        $template  = Configuration::getFor(
            'hc_whatsapp_template_enroute',
            'Namaste {patient_name}! Aapka phlebotomist {phlebotomist_name} aapke ghar ki taraf aa raha hai. Kripya ready rahein.',
            $companyId
        );
        $message = $this->buildMessage($template, $visit);
        $phone   = $visit->invoice->patient->phone ?? null;

        if ($phone) {
            if (Configuration::getFor('hc_notify_whatsapp', '0', $companyId) === '1') {
                $this->sendWhatsApp($phone, $message, $companyId);
            }
            if (Configuration::getFor('hc_notify_sms', '0', $companyId) === '1') {
                $this->sendSms($phone, $message, $companyId);
            }
        }
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    private function buildMessage(string $template, HomeCollection $visit): string
    {
        $slot = $visit->scheduled_slot_start
            ? date('h:i A', strtotime($visit->scheduled_slot_start))
            : '';

        return str_replace(
            ['{patient_name}', '{date}', '{slot}', '{phlebotomist_name}', '{phlebotomist_phone}'],
            [
                $visit->invoice->patient->name    ?? '',
                $visit->scheduled_date?->format('d M Y') ?? '',
                $slot,
                $visit->phlebotomist->name         ?? '',
                $visit->phlebotomist->phone        ?? '',
            ],
            $template
        );
    }

    private function sendWhatsApp(string $phone, string $message, int $companyId): void
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 10) {
            $phone = '91' . $phone;
        }
        $url = "https://wa.me/{$phone}?text=" . urlencode($message);

        Log::channel('daily')->info("[HC WhatsApp] Would send to: {$phone}", [
            'company_id' => $companyId,
            'url'        => $url,
        ]);

        // TODO: Integrate WhatsApp Business API / chosen Indian provider
    }

    private function sendSms(string $phone, string $message, int $companyId): void
    {
        $provider = Configuration::getFor('hc_sms_provider', 'textlocal', $companyId);
        $apiKey   = Configuration::getFor('hc_sms_api_key', '', $companyId);
        $sender   = Configuration::getFor('hc_sms_sender_id', 'LABSMS', $companyId);

        Log::channel('daily')->info("[HC SMS] Would send via {$provider} to: {$phone}", [
            'company_id' => $companyId,
            'message'    => $message,
        ]);

        // TODO: Implement once Indian SMS provider is confirmed
        match ($provider) {
            'textlocal' => $this->sendViaTextLocal($phone, $message, $apiKey, $sender),
            'msg91'     => $this->sendViaMsg91($phone, $message, $apiKey, $sender),
            default     => Log::warning("[HC SMS] Unknown provider: {$provider}"),
        };
    }

    private function sendViaTextLocal(string $phone, string $message, string $apiKey, string $sender): void
    {
        if (! $apiKey) {
            Log::info('[HC SMS TextLocal] API key not configured — skipping.');
            return;
        }
        // https://api.textlocal.in/send/?apikey=...&numbers=...&message=...&sender=...
        // TODO: Implement via Http::post()
    }

    private function sendViaMsg91(string $phone, string $message, string $apiKey, string $sender): void
    {
        if (! $apiKey) {
            Log::info('[HC SMS MSG91] API key not configured — skipping.');
            return;
        }
        // TODO: Implement via Http::post() using MSG91 Flow API
    }
}
