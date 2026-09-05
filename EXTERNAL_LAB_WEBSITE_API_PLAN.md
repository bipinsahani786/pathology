# Pathology SaaS - External Website REST API & Online Booking Plan

> **Document Version:** 1.0  
> **Target Audience:** Backend Developers, Frontend Developers, Lab Owners  
> **Status:** Draft / Ready for Implementation  

---

## 📌 Executive Summary
This document outlines the complete architecture, database schema, and RESTful API specifications required to connect external lab websites (built on WordPress, Next.js, React, Vue, HTML, or Mobile Apps) with this Pathology SaaS platform.

When implemented, an external website will be able to:
1. Fetch and display all active **Branches & Collection Centers** with addresses and contact numbers.
2. Fetch and display **Tests & Health Packages** with live pricing (MRP, offer price, sample type, fasting instructions, and TAT hours).
3. Display **Package Parameters** (all linked tests expanded with their individual parameter lists).
4. Accept **Online Patient Bookings** (Home Collection vs Lab Visit) with preferred date & time slots.
5. Track booking status and let patients **Download Final PDF Reports** directly from the lab's website.

---

## 1. Multi-Tenant Security & Authentication

Every lab registered on this SaaS operates with complete data isolation (`company_id`).

### Authentication Method:
Each request from the external website must include the lab's unique API Key in the HTTP header:

```http
X-Lab-Api-Key: lab_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxx
Content-Type: application/json
Accept: application/json
```

### Architecture Flow:
```
[External Lab Website / Mobile App]
                │
                │ HTTPS Request (Header: X-Lab-Api-Key)
                ▼
[Pathology SaaS API Gateway: /api/v1/*]
                │
                ├─► CORS Middleware (Allows lab's custom domain)
                ├─► Rate Limiter (60 requests/minute per IP)
                └─► VerifyLabApiKey Middleware:
                        - Looks up Company via api_key
                        - Scopes all queries to company_id
                │
                ▼
[Database: branches, lab_tests, web_bookings, invoices]
```

---

## 2. Database Schema Changes Required

### A. Add `api_key` to `companies` table:
```php
// Migration: add_api_key_to_companies_table.php
Schema::table('companies', function (Blueprint $table) {
    $table->string('api_key', 64)->nullable()->unique()->after('status');
    $table->string('api_allowed_origin')->nullable()->after('api_key'); // e.g. https://dynamiclab.in
    $table->boolean('api_enabled')->default(true)->after('api_allowed_origin');
});
```

### B. New Table: `web_bookings`
A dedicated table to hold incoming online bookings from external websites:
```php
// Migration: create_web_bookings_table.php
Schema::create('web_bookings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
    $table->foreignId('patient_id')->nullable()->constrained('users')->nullOnDelete();
    
    $table->string('booking_reference', 30)->unique(); // e.g. WB-2609-0001
    
    // Patient Details
    $table->string('patient_name');
    $table->string('patient_phone', 20);
    $table->string('patient_email')->nullable();
    $table->integer('patient_age')->nullable();
    $table->string('patient_age_type', 10)->default('Years'); // Years, Months, Days
    $table->string('patient_gender', 10)->nullable();
    $table->text('patient_address')->nullable();
    $table->string('patient_pincode', 10)->nullable();
    
    // Collection Details
    $table->string('collection_type', 30)->default('lab_visit'); // home_collection, lab_visit
    $table->date('preferred_date');
    $table->string('preferred_time_slot', 50)->nullable();
    
    // Tests & Pricing Payload
    $table->json('items_payload'); // [{lab_test_id: 12, name: "CBC", price: 350, type: "test"}]
    $table->decimal('total_amount', 10, 2)->default(0);
    $table->string('prescription_file')->nullable();
    $table->text('remarks')->nullable();
    
    // Order & Payment Status
    $table->string('payment_method', 30)->default('pay_on_collection');
    $table->string('payment_status', 20)->default('Unpaid'); // Unpaid, Paid
    $table->string('status', 30)->default('Pending'); // Pending, Confirmed, Assigned, Completed, Cancelled
    
    // Linked Invoice (once accepted by staff)
    $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
    $table->timestamps();
    
    $table->index(['company_id', 'status']);
    $table->index(['patient_phone', 'company_id']);
});
```

---

## 3. Detailed REST API Endpoints Specification

Base URL: `https://your-pathology-domain.com/api/v1`

---

### 1. `GET /branches` (List Branches & Centers)
Fetch all branches/centers of the lab with address and phone.

* **Headers:** `X-Lab-Api-Key: {KEY}`
* **Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Dehradun Main Lab",
      "type": "main_lab",
      "address": "Lane No 2, Near Sakini Hospital, Ring Road, Dehradun",
      "contact_number": "+91-9876543210",
      "is_main": true
    },
    {
      "id": 2,
      "name": "Rajpur Road Collection Center",
      "type": "collection_center",
      "address": "Opp Clock Tower, Rajpur Road, Dehradun",
      "contact_number": "+91-9876543211",
      "is_main": false
    }
  ]
}
```

---

### 2. `GET /departments` (Categories)
Fetch all test departments (Haematology, Biochemistry, etc.).

* **Headers:** `X-Lab-Api-Key: {KEY}`
* **Response (200 OK):**
```json
{
  "success": true,
  "data": [
    { "id": 1, "name": "Haematology", "tests_count": 14 },
    { "id": 2, "name": "Biochemistry", "tests_count": 28 },
    { "id": 3, "name": "Microbiology", "tests_count": 8 }
  ]
}
```

---

### 3. `GET /tests` (Individual Tests Catalog)
Returns all active single tests (`is_package = false`).

* **Query Parameters:**
  * `department_id` (optional): Filter by category
  * `search` (optional): Search test name or code (e.g. `cbc`, `glucose`)
  * `page` (optional): Page number (default: 1)
  * `per_page` (optional): Items per page (default: 20)
* **Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 12,
      "test_code": "CBC",
      "name": "Complete Blood Count (CBC)",
      "department": "Haematology",
      "mrp": 350.00,
      "sample_type": "Whole Blood EDTA / 3 mL",
      "tat_hours": 4,
      "method": "Automated Cell Counter",
      "description": "Complete hemogram evaluating RBC, WBC, platelets.",
      "parameters_count": 14
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 4,
    "total": 68
  }
}
```

---

### 4. `GET /packages` & `GET /packages/{id}` (Health Packages)
Returns packages (`is_package = true`) with their linked tests and inner parameters.

* **Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 45,
      "name": "Full Body Health Checkup - Advanced",
      "mrp": 2499.00,
      "description": "Overnight fasting 10-12 hours required.",
      "total_tests_included": 2,
      "total_parameters_included": 18,
      "included_tests": [
        {
          "test_id": 12,
          "name": "Complete Blood Count (CBC)",
          "parameters": [
            { "name": "Hemoglobin (Hb)", "unit": "g/dL" },
            { "name": "Total RBC Count", "unit": "million/cumm" },
            { "name": "Total WBC Count", "unit": "cells/cumm" },
            { "name": "Platelet Count", "unit": "lakhs/cumm" }
          ]
        },
        {
          "test_id": 18,
          "name": "Lipid Profile",
          "parameters": [
            { "name": "Total Cholesterol", "unit": "mg/dL" },
            { "name": "Triglycerides", "unit": "mg/dL" },
            { "name": "HDL Cholesterol", "unit": "mg/dL" },
            { "name": "LDL Cholesterol", "unit": "mg/dL" }
          ]
        }
      ]
    }
  ]
}
```

---

### 5. `POST /bookings` (Submit Online Booking)
Submits an online booking from the website.

* **Headers:** `X-Lab-Api-Key: {KEY}`, `Content-Type: application/json`
* **Request Body:**
```json
{
  "branch_id": 1,
  "collection_type": "home_collection",
  "preferred_date": "2026-09-06",
  "preferred_time_slot": "08:00 AM - 09:00 AM",
  "patient": {
    "name": "Sunita Devi",
    "phone": "9876543210",
    "email": "sunita@example.com",
    "age": 42,
    "age_type": "Years",
    "gender": "Female",
    "address": "House 14, Lane 3, Rajpur Road, Dehradun",
    "pincode": "248001"
  },
  "items": [
    { "lab_test_id": 45 },
    { "lab_test_id": 12 }
  ],
  "remarks": "Please call 10 minutes before arrival",
  "payment_method": "pay_on_collection"
}
```

* **Response (201 Created):**
```json
{
  "success": true,
  "message": "Booking received successfully!",
  "data": {
    "booking_reference": "WB-2609-0012",
    "status": "Pending",
    "total_amount": 2849.00,
    "collection_type": "home_collection",
    "preferred_date": "2026-09-06",
    "preferred_time_slot": "08:00 AM - 09:00 AM",
    "patient_name": "Sunita Devi",
    "phone": "9876543210"
  }
}
```

---

### 6. `POST /reports/track` (Report Tracker)
Allows patients to track their report status and download the PDF.

* **Request Body:**
```json
{
  "identifier": "INV-2609-0045",
  "phone": "9876543210"
}
```

* **Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "patient_name": "Sunita Devi",
    "invoice_number": "INV-2609-0045",
    "invoice_date": "04/09/2026",
    "report_status": "Completed",
    "is_downloadable": true,
    "download_url": "https://your-pathology-domain.com/v/eyJpaZCI6NDV9"
  }
}
```

---

## 4. Lab Staff Dashboard Integration (Livewire)

When a booking arrives from the external website:
1. A new screen **"Web Bookings"** (`App\Livewire\Lab\WebBookingManager`) displays incoming orders in real-time.
2. The staff sees patient details, collection type (Home vs Lab), selected tests, and preferred slot.
3. Staff clicks **"Accept & Generate Invoice"**:
   - System auto-creates the `User` + `PatientProfile` (if not already existing).
   - System generates an `Invoice` with items pre-filled.
   - Status updates to `Converted to Invoice`.
   - The test immediately appears in `ResultEntryManager`!

---

## 5. Sample Frontend Integration Snippet (JavaScript)

```javascript
// Example: Fetch tests from external website
async function fetchLabTests() {
  const response = await fetch('https://your-pathology-domain.com/api/v1/tests', {
    headers: {
      'X-Lab-Api-Key': 'lab_live_xxxxxxxxxxxxxxxxxxxxxxxx',
      'Accept': 'application/json'
    }
  });
  const result = await response.json();
  if (result.success) {
    console.log("Lab Tests:", result.data);
  }
}
```

---
*File saved on: 2026-09-04 at project root as `EXTERNAL_LAB_WEBSITE_API_PLAN.md`.*
