# Implementation Plan v3 — Phlebotomist (Home Collection) Module
**Project:** Pathology Lab SaaS | **Branch:** bipin | **Laravel 12 + Livewire 3 + PostgreSQL**

> v3 additions: Complete Feature Gating spec (sab kuch hide), Decisions locked (Flat Fee ✅, GPS Optional ✅, Indian SMS TBD ✅)
> v2 additions: SuperAdmin Plan Gating, GPS Location Tracking, Barcode Vial Print,
> On-site Test Addition, Full Status Lifecycle, Schedule System, Notification Service

---

## 🚀 Complete Feature Set (v2)

| Feature | Status | Notes |
|---|---|---|
| SuperAdmin Plan Gating | 🆕 NEW | Plan ke `features['home_collection']` se toggle |
| Phlebotomist CRUD (Admin) | Phase 3 | DoctorManager pattern |
| GPS Location Tracking | 🆕 NEW | Lat/Lng capture + Google Maps link |
| POS Integration | Phase 4 | Home Collection fields, HC fee auto-add |
| Barcode Vial Print | 🆕 NEW | Phlebotomist portal se existing BarcodeController reuse |
| On-site Test Addition | 🆕 NEW | Phlebotomist additional tests add kar sakta hai |
| Full Status Lifecycle (8 steps) | 🆕 NEW | Pending → Assigned → En Route → Arrived → Collected → Dispatched → Received → Ready |
| Schedule System | 🆕 NEW | Time slots, conflict detection, daily schedule view |
| Home Visit Admin Dashboard | Phase 5 | Filters, reassignment, bulk status |
| Phlebotomist Mobile Portal | Phase 6 | Today's tasks, collect, payment |
| Commission Tracking | Phase 7 | Per-visit settlement |
| Notification Service | 🆕 NEW | Abstracted SMS + WhatsApp — future-ready |
| Settings Integration | Phase 8 | HC fee, module toggle, per-branch |

---

## 📁 Complete File Map

---

### Phase 0 — SuperAdmin Plan Gating (FIRST PRIORITY)

> **CONFIRMED DECISION:** Phlebotomist module POORA hide rahega jab tak SuperAdmin
> us specific lab ke plan me `home_collection` feature ON nahi karta.
> Exact same pattern as `inventory`, `whatsapp_custom`.

---

#### ⛔ Complete Hide List — Jab `home_collection = false` ho

Jab SuperAdmin ne kisi lab ke plan me home_collection OFF rakha hai, toh **yeh sab kuch
bilkul dikh nahi** aayega us lab ke kisi bhi user ko:

| Element | Location | Hide Method |
|---|---|---|
| **Phlebotomists** sidebar link | `sidebar.blade.php` | `@if(plan->features['home_collection'])` |
| **Home Visits** sidebar link | `sidebar.blade.php` | Same feature gate |
| **Home Collection** tab in Settings | `SettingsManager` view | Same feature gate |
| **Collection Type = "Home Collection"** option in POS dropdown | `pos-manager.blade.php` | Filter enum options |
| **Home Collection** option in POS Edit | `pos-edit-manager.blade.php` | Filter enum options |
| **Phlebotomist assign** dropdown in POS | `pos-manager.blade.php` | Conditional show |
| `/lab/phlebotomists` route | `PhlebotomistManager.mount()` | `abort(403)` if no feature |
| `/lab/home-visits` route | `HomeVisitManager.mount()` | `abort(403)` if no feature |
| `/phlebotomist/dashboard` route | `PhlebotomistDashboard.mount()` | Check company plan |

**Double-layer protection:**
1. **UI layer** — Blade template me show hi nahi karo
2. **Backend layer** — mount() me abort(403) — agar koi direct URL se jaane ki koshish kare

---

#### [MODIFY] `app/Livewire/Admin/PlanManager.php`

New property add karo (existing `enable_outsourcing` ke baad):
```php
public $has_home_collection = false;
public $max_phlebotomists = 2;
```

`resetDefaultFeatures()` me:
```php
$this->has_home_collection = false;
$this->max_phlebotomists = 2;
```

`store()` validation me:
```php
'has_home_collection' => 'boolean',
'max_phlebotomists'   => 'required|integer|min:0',
```

`$finalFeaturesArr` me add karo:
```php
['key' => 'home_collection',    'value' => $this->has_home_collection],
['key' => 'max_phlebotomists',  'value' => $this->max_phlebotomists],
```

`edit($id)` me map karo:
```php
$this->has_home_collection = $f['home_collection']   ?? false;
$this->max_phlebotomists   = $f['max_phlebotomists'] ?? 2;
```

---

#### [MODIFY] `resources/views/layouts/partials/sidebar.blade.php` — Gating

```php
// Feature gate — plan level (SuperAdmin controlled)
@php $hasHomeCollection = auth()->user()->company->plan?->features['home_collection'] ?? false; @endphp

@if($hasHomeCollection)
    @can('view phlebotomists')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('lab.phlebotomists') }}">
            <i class="bi bi-person-badge me-2"></i> Phlebotomists
        </a>
    </li>
    @endcan
    @can('view home_collections')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('lab.home.visits') }}">
            <i class="bi bi-house-check me-2"></i> Home Visits
        </a>
    </li>
    @endcan
@endif
// Note: module_home_collection Config toggle is secondary (lab-level off switch)
// Plan feature = SuperAdmin controlled (per lab)
// Configuration::getFor = Lab admin controlled (within their plan)
```

---

#### [MODIFY] POS — `collection_type` dropdown me "Home Collection" option gating

`pos-manager.blade.php` aur `pos-edit-manager.blade.php` dono me:
```php
// collection_type dropdown
@php $hasHomeCollection = auth()->user()->company->plan?->features['home_collection'] ?? false; @endphp
<select wire:model.live="collection_type" class="form-select">
    <option value="Center">Walk-in / Center</option>
    <option value="Hospital">Hospital</option>
    @if($hasHomeCollection)
    <option value="Home Collection">Home Collection</option>
    @endif
</select>

// Agar plan me nahi hai aur kisi ne direct Livewire call kiya:
// PosManager::saveBill() me bhi check:
if ($this->collection_type === 'Home Collection') {
    $hasFeature = auth()->user()->company->plan?->features['home_collection'] ?? false;
    if (!$hasFeature) {
        abort(403, 'Home Collection feature is not enabled for your plan.');
    }
}
```

---

#### [MODIFY] All Phlebotomist Livewire Components — Backend Guard

```php
// PhlebotomistManager.php, HomeVisitManager.php, PhlebotomistDashboard.php
public function mount()
{
    $hasFeature = auth()->user()->company->plan?->features['home_collection'] ?? false;
    if (!$hasFeature) {
        abort(403, 'Home Collection module is not available on your current plan.');
    }
    // ... existing mount logic
}
```

---

#### [MODIFY] `app/Livewire/Lab/PhlebotomistManager.php` — Plan limit enforcement

```php
// store() me, agar create kar rahe ho:
$maxPhlebotomists = $company->plan->features['max_phlebotomists'] ?? 2;
$currentCount = User::whereHas('roles', fn($q) => $q->where('name', 'phlebotomist'))
    ->where('company_id', $companyId)->count();
if ($maxPhlebotomists !== -1 && $currentCount >= $maxPhlebotomists) {
    $this->addError('name', "Your plan allows maximum {$maxPhlebotomists} phlebotomists. Please upgrade.");
    return;
}
```

---

#### [MODIFY] `resources/views/livewire/lab/settings-manager.blade.php` — Settings tab gating

```php
@if(auth()->user()->company->plan?->features['home_collection'] ?? false)
<li class="nav-item">
    <button wire:click="$set('activeTab', 'home_collection')" class="nav-link">
        Home Collection
    </button>
</li>
@endif

// Tab content bhi same gate me:
@if($activeTab === 'home_collection' && (auth()->user()->company->plan?->features['home_collection'] ?? false))
    ... HC settings form ...
@endif
```

---

### Phase 1 — Database & Migrations (Updated)

---

#### [NEW] `create_phlebotomist_profiles_table.php`

```
phlebotomist_profiles
  - id
  - user_id (FK → users, cascade)
  - company_id (FK → companies, cascade)
  - vehicle_number string nullable
  - vehicle_type enum[Bike, Car, Scooty, Other] nullable
  - commission_per_visit decimal(8,2) default 0
  - is_available boolean default true  -- Admin manually toggle (e.g., leave day)
  - working_hours_start time nullable  -- e.g., 08:00
  - working_hours_end time nullable    -- e.g., 18:00
  - service_radius_km decimal(5,2) nullable  -- Future: distance-based filtering
  - timestamps
```

---

#### [NEW] `create_home_collections_table.php` (v2 — GPS + Status Lifecycle)

```
home_collections
  - id
  - company_id (FK → companies, cascade)
  - branch_id (FK → branches, nullable, nullOnDelete)
  - invoice_id (FK → invoices, cascade)
  - patient_id (FK → users, cascade)
  - phlebotomist_id (FK → users, nullable, nullOnDelete)
  - assigned_by (FK → users, nullable, nullOnDelete)

  -- Address & Location
  - collection_address text
  - collection_landmark string nullable
  - collection_lat decimal(10,7) nullable   -- GPS latitude (browser Geolocation API)
  - collection_lng decimal(10,7) nullable   -- GPS longitude
  - google_place_id string nullable         -- Future: Google Places autocomplete

  -- Schedule
  - scheduled_date date                     -- Appointment date
  - scheduled_slot_start time              -- e.g., 09:00
  - scheduled_slot_end time               -- e.g., 10:00 (1-hour slots)

  -- Status Lifecycle Timestamps
  - assigned_at datetime nullable
  - en_route_at datetime nullable          -- Phlebotomist ne nikal liya
  - arrived_at datetime nullable           -- Patient ke ghar pahunch gaya
  - collected_at datetime nullable         -- Sample le liya
  - dispatched_at datetime nullable        -- Lab ki taraf roana
  - received_at datetime nullable          -- Lab me sample mila (lab staff marks)

  -- Status
  - status enum[Pending, Assigned, En Route, Arrived, Collected, Dispatched, Received, Cancelled]
    default Pending

  -- Phlebotomist GPS Tracking (collected karte waqt ka location)
  - collected_lat decimal(10,7) nullable
  - collected_lng decimal(10,7) nullable

  -- Additional Info
  - notes text nullable               -- Admin/phlebotomist notes
  - cancellation_reason string nullable
  - cancelled_by (FK → users, nullable, nullOnDelete)

  - timestamps
  - index(company_id, status)
  - index(phlebotomist_id, scheduled_date)
  - index(invoice_id)
```

---

#### [NEW] `create_visit_status_logs_table.php`

Full audit trail of all status changes:
```
visit_status_logs
  - id
  - home_collection_id (FK → home_collections, cascade)
  - from_status string nullable
  - to_status string
  - changed_by (FK → users, nullable, nullOnDelete)
  - latitude decimal(10,7) nullable    -- GPS at time of status change
  - longitude decimal(10,7) nullable
  - notes text nullable
  - created_at (no updated_at — logs are immutable)
```

---

#### [MODIFY] `add_home_collection_fields_to_invoices.php`

```
invoices table me add karo:
  - phlebotomist_id FK → users (nullable, nullOnDelete) — after collection_type
  - home_collection_address text nullable
  - home_collection_lat decimal(10,7) nullable
  - home_collection_lng decimal(10,7) nullable
  - home_scheduled_date date nullable
  - home_scheduled_slot_start time nullable
  - home_scheduled_slot_end time nullable
```

---

#### [NEW] `add_phlebotomist_permissions.php`

```
Permissions to create:
  - view phlebotomists
  - create phlebotomists
  - edit phlebotomists
  - delete phlebotomists
  - view home_collections
  - assign home_collections
  - update home_collection_status
  - add tests to home_collection      -- On-site test addition permission

lab_admin role ko saare permissions milenge.
phlebotomist role ko milega:
  - update home_collection_status
  - add tests to home_collection
```

---

#### [MODIFY] `add_phlebotomist_settings_to_configurations.php` (via existing Config system)

New config keys jo `configurations` table me store honge:
```
home_collection_fee          -- e.g., "200"
home_collection_fee_label    -- e.g., "Home Collection Charge"
home_collection_slot_duration -- e.g., "60" (minutes per slot)
home_collection_slots        -- JSON: ["08:00-09:00","09:00-10:00",...] or auto-generated
module_home_collection       -- "1" / "0"
hc_notify_sms                -- "1" / "0" — SMS send karo assignment pe
hc_notify_whatsapp           -- "1" / "0" — WhatsApp send karo assignment pe
hc_sms_provider              -- "textlocal" / "msg91" / "twilio"
hc_sms_api_key               -- encrypted API key
hc_sms_sender_id             -- Sender ID
hc_whatsapp_template_assign  -- Template message for assignment
hc_whatsapp_template_enroute -- Template message when en route
```

---

### Phase 2 — Models

---

#### [NEW] `app/Models/PhlebotomistProfile.php`

```php
class PhlebotomistProfile extends Model
{
    use \App\Traits\Auditable, BelongsToCompany;
    protected $guarded = [];
    protected $casts = [
        'is_available'       => 'boolean',
        'working_hours_start'=> 'string',
        'working_hours_end'  => 'string',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    // Check karo kya phlebotomist is slot pe available hai
    public function isAvailableForSlot(string $date, string $slotStart, string $slotEnd): bool
    {
        if (!$this->is_available) return false;
        return !HomeCollection::where('phlebotomist_id', $this->user_id)
            ->where('scheduled_date', $date)
            ->where('status', '!=', 'Cancelled')
            ->where(function($q) use ($slotStart, $slotEnd) {
                $q->whereBetween('scheduled_slot_start', [$slotStart, $slotEnd])
                  ->orWhereBetween('scheduled_slot_end', [$slotStart, $slotEnd]);
            })->exists();
    }
}
```

---

#### [NEW] `app/Models/HomeCollection.php`

```php
class HomeCollection extends Model
{
    use BelongsToCompany;
    protected $guarded = [];
    protected $casts = [
        'scheduled_date'     => 'date',
        'assigned_at'        => 'datetime',
        'en_route_at'        => 'datetime',
        'arrived_at'         => 'datetime',
        'collected_at'       => 'datetime',
        'dispatched_at'      => 'datetime',
        'received_at'        => 'datetime',
    ];

    // Status order for progression validation
    public const STATUS_ORDER = [
        'Pending', 'Assigned', 'En Route', 'Arrived',
        'Collected', 'Dispatched', 'Received', 'Cancelled'
    ];

    public function invoice()         { return $this->belongsTo(Invoice::class); }
    public function patient()         { return $this->belongsTo(User::class, 'patient_id'); }
    public function phlebotomist()    { return $this->belongsTo(User::class, 'phlebotomist_id'); }
    public function assignedBy()      { return $this->belongsTo(User::class, 'assigned_by'); }
    public function cancelledBy()     { return $this->belongsTo(User::class, 'cancelled_by'); }
    public function branch()          { return $this->belongsTo(Branch::class); }
    public function statusLogs()      { return $this->hasMany(VisitStatusLog::class); }

    // Google Maps link for phlebotomist portal
    public function getGoogleMapsLinkAttribute(): string
    {
        if ($this->collection_lat && $this->collection_lng) {
            return "https://maps.google.com/?q={$this->collection_lat},{$this->collection_lng}";
        }
        return "https://maps.google.com/?q=" . urlencode($this->collection_address);
    }

    // Update status with audit log
    public function transitionStatus(string $newStatus, int $userId, ?float $lat = null, ?float $lng = null, ?string $notes = null): void
    {
        $oldStatus = $this->status;
        $timestamps = [
            'Assigned'   => 'assigned_at',
            'En Route'   => 'en_route_at',
            'Arrived'    => 'arrived_at',
            'Collected'  => 'collected_at',
            'Dispatched' => 'dispatched_at',
            'Received'   => 'received_at',
        ];
        $updateData = ['status' => $newStatus];
        if (isset($timestamps[$newStatus])) {
            $updateData[$timestamps[$newStatus]] = now();
        }
        if ($lat && $lng && $newStatus === 'Collected') {
            $updateData['collected_lat'] = $lat;
            $updateData['collected_lng'] = $lng;
        }
        $this->update($updateData);

        // Audit log entry
        VisitStatusLog::create([
            'home_collection_id' => $this->id,
            'from_status'        => $oldStatus,
            'to_status'          => $newStatus,
            'changed_by'         => $userId,
            'latitude'           => $lat,
            'longitude'          => $lng,
            'notes'              => $notes,
        ]);

        // Sync invoice sample_status
        $sampleStatusMap = [
            'Collected'  => 'Sample Collected',
            'Dispatched' => 'Dispatched',
            'Received'   => 'Received',
        ];
        if (isset($sampleStatusMap[$newStatus])) {
            $this->invoice->update(['sample_status' => $sampleStatusMap[$newStatus]]);
        }
    }
}
```

---

#### [NEW] `app/Models/VisitStatusLog.php`

```php
class VisitStatusLog extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['created_at' => 'datetime'];

    public function homeCollection() { return $this->belongsTo(HomeCollection::class); }
    public function changedBy()      { return $this->belongsTo(User::class, 'changed_by'); }
}
```

---

#### [MODIFY] `app/Models/User.php`

```php
public function phlebotomistProfile()
{
    return $this->hasOne(PhlebotomistProfile::class);
}
public function homeCollectionsAsPhlebotomist()
{
    return $this->hasMany(HomeCollection::class, 'phlebotomist_id');
}
```

#### [MODIFY] `app/Models/Invoice.php`

```php
public function homeCollection()   { return $this->hasOne(HomeCollection::class); }
public function phlebotomist()     { return $this->belongsTo(User::class, 'phlebotomist_id'); }
```

---

### Phase 3 — Notification Service (Future-Ready Architecture)

> Ek abstracted service banao taaki kal SMS provider change karna ho ya WhatsApp add karna ho,
> sirf ek jagah change hoga. Phlebotomist module se directly call hoga.

---

#### [NEW] `app/Services/NotificationService.php`

```php
namespace App\Services;

use App\Models\Configuration;
use App\Models\HomeCollection;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification when phlebotomist is assigned.
     * Future me: SMS + WhatsApp dono support karega.
     */
    public function notifyPatientAssigned(HomeCollection $visit): void
    {
        $companyId = $visit->company_id;

        $message = $this->buildMessage(
            Configuration::getFor('hc_whatsapp_template_assign',
                'Namaste {patient_name}! Aapka home sample collection {date} ko {slot} baje ke liye confirm ho gaya hai. Phlebotomist: {phlebotomist_name} ({phlebotomist_phone}). Dhanyawad!',
                $companyId
            ),
            $visit
        );

        // WhatsApp
        if (Configuration::getFor('hc_notify_whatsapp', '0', $companyId) === '1') {
            $this->sendWhatsApp($visit->invoice->patient->phone, $message, $companyId);
        }

        // SMS
        if (Configuration::getFor('hc_notify_sms', '0', $companyId) === '1') {
            $this->sendSms($visit->invoice->patient->phone, $message, $companyId);
        }
    }

    public function notifyPatientEnRoute(HomeCollection $visit): void
    {
        $companyId = $visit->company_id;
        $message = $this->buildMessage(
            Configuration::getFor('hc_whatsapp_template_enroute',
                'Namaste {patient_name}! Aapka phlebotomist {phlebotomist_name} aapke ghar aa raha hai. Kripya ready rahein.',
                $companyId
            ),
            $visit
        );
        if (Configuration::getFor('hc_notify_whatsapp', '0', $companyId) === '1') {
            $this->sendWhatsApp($visit->invoice->patient->phone, $message, $companyId);
        }
        if (Configuration::getFor('hc_notify_sms', '0', $companyId) === '1') {
            $this->sendSms($visit->invoice->patient->phone, $message, $companyId);
        }
    }

    private function buildMessage(string $template, HomeCollection $visit): string
    {
        $slot = $visit->scheduled_slot_start
            ? date('h:i A', strtotime($visit->scheduled_slot_start))
            : '';
        return str_replace(
            ['{patient_name}', '{date}', '{slot}', '{phlebotomist_name}', '{phlebotomist_phone}'],
            [
                $visit->patient->name ?? '',
                $visit->scheduled_date?->format('d M Y') ?? '',
                $slot,
                $visit->phlebotomist->name ?? '',
                $visit->phlebotomist->phone ?? '',
            ],
            $template
        );
    }

    private function sendWhatsApp(string $phone, string $message, int $companyId): void
    {
        // WhatsApp business API integration (future scope)
        // Currently: reuse existing getWhatsappLink() approach
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 10) $phone = '91' . $phone;
        $url = "https://api.whatsapp.com/send?phone={$phone}&text=" . urlencode($message);
        Log::channel('daily')->info("HC WhatsApp notification queued: {$url}");
        // TODO: Queue a job to call WhatsApp Business API
    }

    private function sendSms(string $phone, string $message, int $companyId): void
    {
        $provider = Configuration::getFor('hc_sms_provider', 'textlocal', $companyId);
        $apiKey   = Configuration::getFor('hc_sms_api_key', '', $companyId);
        $sender   = Configuration::getFor('hc_sms_sender_id', 'LABSMS', $companyId);

        Log::channel('daily')->info("HC SMS notification queued via {$provider} to {$phone}");

        // Provider-agnostic dispatch
        match($provider) {
            'textlocal' => $this->sendViaTextLocal($phone, $message, $apiKey, $sender),
            'msg91'     => $this->sendViaMsg91($phone, $message, $apiKey, $sender),
            'twilio'    => $this->sendViaTwilio($phone, $message, $apiKey),
            default     => Log::warning("Unknown SMS provider: {$provider}"),
        };
    }

    private function sendViaTextLocal(string $phone, string $message, string $apiKey, string $sender): void
    {
        // TextLocal API call (India-popular)
        // https://api.textlocal.in/send/?apikey=...&numbers=...&message=...&sender=...
        // TODO: Implement via Laravel HTTP client
    }

    private function sendViaMsg91(string $phone, string $message, string $apiKey, string $sender): void
    {
        // MSG91 Flow/Template API
        // TODO: Implement via Laravel HTTP client
    }

    private function sendViaTwilio(string $phone, string $message, string $apiKey): void
    {
        // Twilio REST API (International)
        // TODO: Implement via Twilio SDK
    }
}
```

> **Integration point:** `HomeVisitManager::assignPhlebotomist()` aur `PhlebotomistDashboard::updateStatus()` me inject:
> ```php
> app(NotificationService::class)->notifyPatientAssigned($visit);
> ```

---

### Phase 4 — Livewire Components

---

#### [NEW] `app/Livewire/Lab/PhlebotomistManager.php`

Pattern: DoctorManager.php ke saath identical. Additional fields:
```php
public $vehicle_number, $vehicle_type, $commission_per_visit = 0;
public $is_available = true;
public $working_hours_start = '08:00';
public $working_hours_end = '18:00';
```

---

#### [NEW] `app/Livewire/Lab/HomeVisitManager.php`

State:
```php
public $searchTerm = '', $filterStatus = '', $filterDate = '', $filterPhlebotomistId = '';
public $selectedVisitId = null, $phlebotomist_id = null;
public $isAssignModalOpen = false, $isStatusModalOpen = false;
public $newStatus = '', $adminNotes = '';
// Schedule view
public $viewMode = 'list'; // 'list' or 'schedule'
public $scheduleDate;
```

Key methods:
```
assignPhlebotomist()     — HomeCollection::transitionStatus('Assigned', ...) + NotificationService
updateStatus($id, $s)    — transitionStatus() call
getScheduleData()        — Ek din ka slot-wise grid — kaun kahan hai
exportVisits()           — Excel export (PhpSpreadsheet)
bulkAssign()             — Multiple pending visits ek hi phlebotomist ko assign
```

---

#### [NEW] `app/Livewire/Phlebotomist/PhlebotomistDashboard.php` (v2)

New features in v2:
```php
// Location capture
public $capturedLat = null;
public $capturedLng = null;

// On-site test addition
public $isAddTestModalOpen = false;
public $testSearchQuery = '';
public $additionalTests = []; // Tests to add

// Status progression
public function updateStatus(int $visitId, string $newStatus): void
{
    $visit = HomeCollection::where('phlebotomist_id', auth()->id())->findOrFail($visitId);
    $visit->transitionStatus($newStatus, auth()->id(), $this->capturedLat, $this->capturedLng);

    if ($newStatus === 'En Route') {
        app(NotificationService::class)->notifyPatientEnRoute($visit);
    }
    $this->capturedLat = $this->capturedLng = null;
    session()->flash('message', "Status updated to: {$newStatus}");
}

// Print barcode — redirect to existing BarcodeController route
public function printBarcode(int $invoiceId): void
{
    $this->redirect(route('lab.invoice.barcode.stickers', $invoiceId));
    // Existing route: /lab/invoice/{id}/barcode-stickers (BarcodeController@printStickers)
    // Phlebotomist portal me bhi yahi route accessible rahegi with middleware check
}

// Add tests on-site
public function searchTests(): void
{
    // Search LabTest by name for this company
}

public function addTestToVisit(int $visitId, int $labTestId): void
{
    $this->authorize('add tests to home_collection');
    $visit = HomeCollection::where('phlebotomist_id', auth()->id())->findOrFail($visitId);
    $invoice = $visit->invoice;
    $labTest = \App\Models\LabTest::where('company_id', $invoice->company_id)->findOrFail($labTestId);

    // Check agar pehle se add nahi hua
    if ($invoice->items()->where('lab_test_id', $labTestId)->exists()) {
        $this->addError('testSearch', 'Yeh test pehle se add hai.');
        return;
    }

    DB::transaction(function () use ($invoice, $labTest) {
        InvoiceItem::create([
            'invoice_id'  => $invoice->id,
            'company_id'  => $invoice->company_id,
            'lab_test_id' => $labTest->id,
            'item_name'   => $labTest->name,
            'price'       => $labTest->price,
            'b2b_price'   => $labTest->b2b_price ?? $labTest->price,
            'quantity'    => 1,
        ]);
        // Recalculate invoice totals
        $newTotal = $invoice->items()->sum(\DB::raw('price * quantity'));
        $invoice->update([
            'subtotal'     => $newTotal,
            'total_amount' => $newTotal - $invoice->discount_amount,
            'due_amount'   => ($newTotal - $invoice->discount_amount) - $invoice->paid_amount,
        ]);

        // TestReport record bhi create karo naye test ke liye
        \App\Models\TestReport::firstOrCreate([
            'invoice_id'  => $invoice->id,
            'company_id'  => $invoice->company_id,
        ]);
    });

    $this->isAddTestModalOpen = false;
    session()->flash('message', "{$labTest->name} added to invoice.");
}
```

---

### Phase 5 — Barcode Vial Print (Phlebotomist Portal)

> **Existing system reuse:** `BarcodeController@printStickers` already exists at route
> `lab.invoice.barcode.stickers`. Phlebotomist portal me bhi same route accessible karao.

#### [MODIFY] `routes/web.php` — Phlebotomist route group me barcode add karo

```php
Route::middleware(['auth'])
    ->prefix('phlebotomist')
    ->name('phlebotomist.')
    ->group(function () {
        Route::get('/dashboard', PhlebotomistDashboard::class)->name('dashboard');

        // Barcode sticker print — existing BarcodeController reuse
        Route::get('/invoice/{id}/barcode-stickers',
            [\App\Http\Controllers\BarcodeController::class, 'printStickers'])
            ->name('invoice.barcode.stickers');

        // Report print for delivered reports
        Route::get('/reports/print/{id}/{template?}',
            [\App\Http\Controllers\ReportPdfController::class, 'download'])
            ->name('reports.print');
    });
```

**BarcodeController middleware update:** Role check add karo:
```php
// BarcodeController.php me guard add karo:
if (!auth()->user()->hasAnyRole(['lab_admin', 'super_admin', 'receptionist', 'phlebotomist'])) {
    abort(403);
}
```

---

### Phase 6 — Schedule System

---

#### Schedule Logic

Time slots auto-generate hongi based on working hours + slot duration:

```php
// In PhlebotomistManager / HomeVisitManager
public function getAvailableSlots(string $date, ?int $phlebotomistId = null): array
{
    $slotDuration = (int) Configuration::getFor('home_collection_slot_duration', 60);
    $startTime = strtotime('08:00');
    $endTime   = strtotime('18:00');
    $slots = [];

    while ($startTime < $endTime) {
        $slotEnd = $startTime + ($slotDuration * 60);
        $slotKey = date('H:i', $startTime) . '-' . date('H:i', $slotEnd);

        if ($phlebotomistId) {
            // Check conflicts
            $booked = HomeCollection::where('phlebotomist_id', $phlebotomistId)
                ->where('scheduled_date', $date)
                ->where('status', '!=', 'Cancelled')
                ->where('scheduled_slot_start', date('H:i', $startTime))
                ->exists();
            $slots[] = ['slot' => $slotKey, 'available' => !$booked];
        } else {
            $slots[] = ['slot' => $slotKey];
        }

        $startTime = $slotEnd;
    }
    return $slots;
}
```

**POS View me slot picker:**
- Date select karo → Phlebotomist select karo → Available slots auto-load (Livewire `wire:model.live`)
- Unavailable slots grey dikhein

**Admin Schedule View (HomeVisitManager):**
- Calendar-style day view
- X-axis: Time slots | Y-axis: Phlebotomist names
- Color coding: Pending=yellow, Assigned=blue, En Route=orange, Collected=green, Cancelled=red

---

### Phase 7 — GPS Location Tracking

---

#### Browser Geolocation API (Frontend)

In `pos-manager.blade.php` (Home Collection section) aur `phlebotomist/dashboard.blade.php`:

```javascript
// Patient ke ghar ka address enter karte waqt — optional GPS capture
function captureLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            @this.set('home_collection_lat', pos.coords.latitude.toFixed(7));
            @this.set('home_collection_lng', pos.coords.longitude.toFixed(7));
        }, function(err) {
            console.warn('Location access denied:', err.message);
        });
    }
}

// Phlebotomist dashboard — status update karte waqt GPS capture
function captureAndUpdateStatus(visitId, newStatus) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            @this.set('capturedLat', pos.coords.latitude.toFixed(7));
            @this.set('capturedLng', pos.coords.longitude.toFixed(7));
            @this.call('updateStatus', visitId, newStatus);
        }, function() {
            // GPS denied — still allow status update without location
            @this.call('updateStatus', visitId, newStatus);
        });
    } else {
        @this.call('updateStatus', visitId, newStatus);
    }
}
```

#### Google Maps Integration

```php
// HomeCollection model me accessor:
public function getGoogleMapsLinkAttribute(): string
{
    if ($this->collection_lat && $this->collection_lng) {
        return "https://maps.google.com/?q={$this->collection_lat},{$this->collection_lng}";
    }
    return "https://maps.google.com/?q=" . urlencode($this->collection_address);
}

// Navigation link (Google Maps turn-by-turn):
public function getNavigationLinkAttribute(): string
{
    $dest = $this->collection_lat
        ? "{$this->collection_lat},{$this->collection_lng}"
        : urlencode($this->collection_address);
    return "https://www.google.com/maps/dir/?api=1&destination={$dest}&travelmode=driving";
}
```

**Phlebotomist Portal Dashboard card:**
```html
<a href="{{ $visit->navigation_link }}" target="_blank" class="btn btn-success">
    <i class="bi bi-map-fill me-1"></i> Navigate
</a>
```

---

### Phase 8 — Settings (Admin)

---

#### [MODIFY] `app/Livewire/Lab/SettingsManager.php`

New "Home Collection" tab — properties:
```php
// Module toggle
public $module_home_collection = true;
// Fee
public $home_collection_fee = 0;
public $home_collection_fee_label = 'Home Collection Charge';
// Slots
public $home_collection_slot_duration = 60; // minutes
// Notifications
public $hc_notify_sms = false;
public $hc_notify_whatsapp = false;
public $hc_sms_provider = 'textlocal';
public $hc_sms_api_key = '';
public $hc_sms_sender_id = '';
public $hc_whatsapp_template_assign = '';
public $hc_whatsapp_template_enroute = '';
```

---

### Phase 9 — Blade Views

---

#### [NEW] `resources/views/livewire/lab/phlebotomist-manager.blade.php`

DoctorManager ke saath identical pattern. Extra fields:
- Vehicle Type (Bike/Car/Scooty/Other)
- Vehicle Number
- Commission Per Visit
- Working Hours (Start → End)
- Available toggle (manual leave marking)

---

#### [NEW] `resources/views/livewire/lab/home-visit-manager.blade.php`

Two view modes:

**List View:**
- Filters: Status, Date, Branch, Phlebotomist
- Table: Invoice No | Patient | Address | Scheduled Slot | Phlebotomist | Status Badge | Actions
- Status badges with colors
- Quick actions: Assign, Update Status, View Invoice, Print Barcode

**Schedule View (Admin):**
- Date selector
- Grid: Rows = Phlebotomists, Columns = Time slots
- Each cell shows patient name (if assigned) with color-coded status
- Click cell to assign/view

---

#### [NEW] `resources/views/livewire/phlebotomist/dashboard.blade.php`

Mobile-first cards (Bootstrap + minimal design):

```
Visit Card:
┌─────────────────────────────────────┐
│ 📍 09:00 - 10:00 | Status Badge     │
│ Patient: Rahul Sharma               │
│ Phone: [📞 Call] [💬 WhatsApp]      │
│ Address: 123, MG Road, Pune         │
│ [🗺️ Navigate] [📍 Capture Location]  │
│─────────────────────────────────────│
│ Tests: CBC, LFT, Blood Sugar        │
│─────────────────────────────────────│
│ Due: ₹500  [💳 Collect Payment]     │
│─────────────────────────────────────│
│ [➕ Add Test]  [🏷️ Print Barcode]   │
│─────────────────────────────────────│
│ Status Actions:                     │
│ [En Route] [Arrived] [Collected]    │
└─────────────────────────────────────┘
```

Status action buttons — GPS capture first, then status update:
```javascript
onclick="captureAndUpdateStatus({{ $visit->id }}, 'Collected')"
```

---

#### [MODIFY] `resources/views/livewire/lab/pos-manager.blade.php`

Home Collection section (wire:show when collection_type === 'Home Collection'):
```
┌──── Home Collection Details ─────────────────┐
│ Address*:  [textarea]                         │
│ Landmark:  [input]                            │
│ [📍 Capture Current Location] (GPS button)   │
│ Lat/Lng display (auto-fill if captured)       │
│                                               │
│ Scheduled Date*: [date picker, min=today]    │
│ Phlebotomist:    [dropdown — optional]        │
│ Time Slot:       [dropdown — loads on change] │
│   (Slots grey out if phlebotomist is busy)    │
└───────────────────────────────────────────────┘
```

---

#### [NEW] `resources/views/layouts/phlebotomist.blade.php`

Minimal mobile layout:
- No sidebar
- Logo + Lab Name header
- Logout button (top-right)
- Date navigation (prev/next day) bar
- Single-column responsive

---

### Phase 10 — Routes (Complete)

---

#### [MODIFY] `routes/web.php`

**Lab routes group me add:**
```php
// Phlebotomist Management (Plan feature gated)
Route::get('/phlebotomists', \App\Livewire\Lab\PhlebotomistManager::class)
    ->name('phlebotomists')
    ->middleware('can:view phlebotomists');

// Home Visit Tracking
Route::get('/home-visits', \App\Livewire\Lab\HomeVisitManager::class)
    ->name('home.visits')
    ->middleware('can:view home_collections');

// AJAX: Available slots for a phlebotomist on a date
Route::get('/home-visits/slots', [\App\Http\Controllers\HomeCollectionController::class, 'getSlots'])
    ->name('home.visits.slots');
```

**New Phlebotomist Portal group:**
```php
Route::middleware(['auth'])
    ->prefix('phlebotomist')
    ->name('phlebotomist.')
    ->group(function () {
        Route::get('/dashboard', \App\Livewire\Phlebotomist\PhlebotomistDashboard::class)
            ->name('dashboard');
        // Barcode vial print (reuses BarcodeController)
        Route::get('/invoice/{id}/barcode-stickers',
            [\App\Http\Controllers\BarcodeController::class, 'printStickers'])
            ->name('invoice.barcode.stickers');
    });
```

**Dashboard redirect update** (`/dashboard` redirect logic):
```php
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('phlebotomist')) {
        return redirect()->route('phlebotomist.dashboard');
    }
    // ... existing lab/admin/partner redirect logic
})->middleware(['auth'])->name('dashboard');
```

---

### Phase 11 — Sidebar Navigation

---

#### [MODIFY] `resources/views/layouts/partials/sidebar.blade.php`

```php
@if(auth()->user()->company->plan?->features['home_collection'] ?? false)
    @if(\App\Models\Configuration::getFor('module_home_collection', '1') === '1')
        @can('view phlebotomists')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('lab.phlebotomists') ? 'active' : '' }}"
               href="{{ route('lab.phlebotomists') }}">
                <i class="bi bi-person-badge me-2"></i> Phlebotomists
            </a>
        </li>
        @endcan
        @can('view home_collections')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('lab.home.visits') ? 'active' : '' }}"
               href="{{ route('lab.home.visits') }}">
                <i class="bi bi-house-check me-2"></i> Home Visits
            </a>
        </li>
        @endcan
    @endif
@endif
```

---

## 📊 Complete Visit Status Lifecycle

```
[POS Billing]
     │
     ▼
  Pending ──────────────────────────────────────┐
     │ Admin assigns phlebotomist               │
     ▼                                          │
  Assigned ─── NotificationService.notifyPatientAssigned()
     │ Phlebotomist portal → "En Route" button  │
     ▼                                          │
  En Route ─── NotificationService.notifyPatientEnRoute()
     │ GPS auto-captured                        │
     ▼                                          │
  Arrived                                       │
     │ "Collected" button + GPS capture         │
     ▼                                          │
  Collected → invoice.sample_status = "Sample Collected"
     │ Phlebotomist lab pe deliver karta hai    │
     ▼                                          │
  Dispatched                                    │
     │ Lab receptionist marks received          │
     ▼                                          │
  Received → invoice.sample_status = "Received" │
     │                                          │
     ▼                                 ─────────┘
  (Result Entry starts)           Cancelled (at any step)
                                  NotificationService.notifyCancelled()
```

Every transition → `VisitStatusLog` entry (with GPS if available)

---

## 🗓️ Implementation Priority Order

| Priority | Phase | Task | Effort |
|---|---|---|---|
| 1 | P0 | PlanManager feature key `home_collection` add | 1 hr |
| 2 | P1 | 5 Migrations (profiles, home_collections, status_logs, invoice cols, permissions) | 2 hrs |
| 3 | P2 | 4 Models (PhlebotomistProfile, HomeCollection, VisitStatusLog + User/Invoice relations) | 2 hrs |
| 4 | P4 | PhlebotomistManager CRUD + View | 2 hrs |
| 5 | P4 | PosManager modifications (fields + HC fee + HomeCollection create) | 3 hrs |
| 6 | P4 | HomeVisitManager (list + assign + status) + View | 3 hrs |
| 7 | P6 | PhlebotomistDashboard portal + Mobile View + Layout | 4 hrs |
| 8 | P5 | Barcode route in phlebotomist group + middleware | 30 min |
| 9 | P6 | On-site test addition (addTestToVisit method + modal) | 2 hrs |
| 10 | P6 | GPS location capture (JS + Livewire) | 1 hr |
| 11 | P6 | Schedule system (slots + conflict detection + admin grid view) | 3 hrs |
| 12 | P3 | NotificationService scaffold (log-only, hooks ready) | 1 hr |
| 13 | P8 | Settings tab (HC fee, slots, notification config) | 2 hrs |
| 14 | P10 | Routes + Sidebar + Dashboard redirect | 1 hr |
| 15 | Future | SMS provider implementation (TextLocal/MSG91) | 3 hrs |
| 16 | Future | WhatsApp Business API implementation | 4 hrs |
| 17 | Future | Distance-based fee (Google Maps Distance Matrix API) | 4 hrs |

---

## ❓ Decisions Log

| # | Question | Decision | Status |
|---|---|---|---|
| Q1 | Home Collection Fee type | **Flat fee** from Configuration (e.g., ₹200). Distance-based = future scope. | ✅ Resolved |
| Q2 | GPS: Required ya Optional? | **Optional** — agar patient deny kare toh address string se Google Maps link | ✅ Resolved |
| Q3 | Test Addition Authorization | **Direct add** by phlebotomist — no approval queue needed | ✅ Resolved |
| Q4 | Slot Duration | **Configurable** via `home_collection_slot_duration` config key (default: 60 min) | ✅ Resolved |
| Q5 | SMS Provider | **Indian provider** (TextLocal ya MSG91) — specific provider confirm baad me. NotificationService me provider-agnostic design hai — ek config change se switch hoga. | 🔄 Pending confirm |
| Q6 | Barcode items | **Same as existing** BarcodeController — all invoice items. No change needed. | ✅ Resolved |
| Q7 | SuperAdmin Feature Gating | **Complete hide** — plan me `home_collection = false` hone pe sidebar, POS option, settings tab, routes sab hide | ✅ Confirmed |

---

## ✅ Verification Plan

```bash
# 1. Migrations
php artisan migrate

# 2. Permissions seed
php artisan db:seed --class=RolesAndPermissionsSeeder

# 3. Regression suite
php artisan test

# Manual checklist
# FEATURE GATING (Critical)
# [ ] SuperAdmin PlanManager me home_collection toggle + max_phlebotomists field dikh raha hai
# [ ] Lab jiske plan me home_collection = false → sidebar me Phlebotomists/Home Visits nahi dikhta
# [ ] Lab jiske plan me home_collection = false → POS me "Home Collection" option nahi dikhta
# [ ] Lab jiske plan me home_collection = false → Settings me Home Collection tab nahi dikhta
# [ ] Direct URL /lab/phlebotomists → plan OFF → 403 error
# [ ] Direct URL /lab/home-visits → plan OFF → 403 error
# [ ] Phlebotomist portal /phlebotomist/dashboard → company plan check → 403 if OFF
# [ ] Phlebotomist create → plan limit enforce ho raha hai (max_phlebotomists)
# [ ] POS Home Collection select → slots load ho rahe hain (plan ON wale labs me)
# [ ] POS save → HomeCollection record, status=Assigned/Pending
# [ ] POS save → HC fee line item auto-add (agar configured)
# [ ] Admin HomeVisitManager → assign phlebotomist → status=Assigned
# [ ] VisitStatusLog → har status change logged hai
# [ ] Phlebotomist portal login → sirf aaj ki visits dikhen
# [ ] En Route button → GPS capture attempt, status update
# [ ] Mark Collected → GPS captured, invoice sample_status = "Sample Collected"
# [ ] Barcode print button → existing barcode sticker view load
# [ ] On-site test add → invoice total recalculate, TestReport record create
# [ ] Collect payment → invoice paid_amount/due_amount update
# [ ] NotificationService.notifyPatientAssigned() → log entry created (no crash)
# [ ] Settings tab → HC fee + notification config save
```
