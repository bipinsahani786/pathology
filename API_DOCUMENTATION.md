# 🌐 Pathology SaaS - External Website REST API Documentation (v1)

This documentation provides complete instructions and endpoint references for integrating your external website (WordPress, Next.js, React, Vue, PHP, or Mobile App) with your Pathology Laboratory Software.

---

## 📌 1. Overview & Authentication

### Base URL
```
https://your-pathology-domain.com/api/v1
```

### Security & Multi-Tenant Isolation
Every request to the API must include your unique Lab API key in the request headers:

| Header Name | Value | Description |
| :--- | :--- | :--- |
| `X-Lab-Api-Key` | `lab_xxxxxxxxxxxxxxxxxxxx` | Secret API key generated in your Lab Settings |
| `Accept` | `application/json` | Expected response format |
| `Content-Type` | `application/json` | Required for `POST` requests |

> [!IMPORTANT]
> **Superadmin Plan Gated Feature**:
> API access is only active if your subscription plan contains the **"Website API & Online Bookings"** feature enabled by the Superadmin. If your plan does not have this feature, all API requests will return `403 Forbidden`.

---

## 🚀 2. API Endpoints Reference

### 2.1 Get Branches & Collection Centers
Returns all active laboratory branches and sample collection centers for your lab.

* **Method**: `GET`
* **Endpoint**: `/api/v1/branches`
* **Headers**: `X-Lab-Api-Key: {your_key}`

#### Example Response:
```json
{
  "success": true,
  "message": "Branches and collection centers retrieved successfully.",
  "data": {
    "branches": [
      {
        "id": 1,
        "name": "Central Diagnostic Lab",
        "address": "45 Healthcare Plaza, Main Road",
        "phone": "+91 9876543210"
      }
    ],
    "collection_centers": [
      {
        "id": 3,
        "branch_id": 1,
        "name": "North City Collection Point",
        "center_code": "CC-NORTH",
        "address": "Shop 12, City Center Mall"
      }
    ]
  }
}
```

---

### 2.2 Get Departments / Categories
Lists active test departments (e.g. Hematology, Biochemistry, Immunology).

* **Method**: `GET`
* **Endpoint**: `/api/v1/departments`
* **Headers**: `X-Lab-Api-Key: {your_key}`

#### Example Response:
```json
{
  "success": true,
  "message": "Departments retrieved successfully.",
  "data": [
    {
      "id": 1,
      "name": "Biochemistry"
    },
    {
      "id": 2,
      "name": "Hematology"
    },
    {
      "id": 3,
      "name": "Serology & Immunology"
    }
  ]
}
```

---

### 2.3 Get Tests Catalog
Retrieves active individual tests with live prices, sample requirements, and turnaround time (TAT).

* **Method**: `GET`
* **Endpoint**: `/api/v1/tests`
* **Query Parameters**:
  * `search` (optional): Filter tests by name or test code (e.g. `?search=lipid` or `?search=CBC`).
  * `department_id` (optional): Filter by department ID (e.g. `?department_id=2`).
  * `per_page` (optional): Number of records per page (default: 50, max: 100).
  * `page` (optional): Page number (e.g. `?page=2`).

#### Example Response:
```json
{
  "success": true,
  "message": "Tests catalog retrieved successfully.",
  "data": {
    "tests": [
      {
        "id": 12,
        "name": "Complete Blood Count (CBC)",
        "test_code": "CBC",
        "department_id": 2,
        "department_name": "Hematology",
        "price": 350.00,
        "sample_type": "EDTA Whole Blood",
        "tat_hours": 6,
        "fasting_required": false,
        "description": "Routine blood test evaluating cellular components."
      },
      {
        "id": 45,
        "name": "Fasting Blood Glucose",
        "test_code": "FBS",
        "department_id": 1,
        "department_name": "Biochemistry",
        "price": 100.00,
        "sample_type": "Sodium Fluoride Plasma",
        "tat_hours": 4,
        "fasting_required": true,
        "description": "Requires 8 to 10 hours overnight fasting."
      }
    ],
    "current_page": 1,
    "last_page": 3,
    "total": 138,
    "per_page": 50
  }
}
```

---

### 2.4 Get Packages Catalog
Retrieves all health packages and wellness profiles (e.g. Full Body Checkup, Diabetic Profile).

* **Method**: `GET`
* **Endpoint**: `/api/v1/packages`
* **Headers**: `X-Lab-Api-Key: {your_key}`

#### Example Response:
```json
{
  "success": true,
  "message": "Test packages retrieved successfully.",
  "data": [
    {
      "id": 8,
      "name": "Executive Full Body Health Checkup",
      "test_code": "EXEC-FULL",
      "price": 1499.00,
      "sample_type": "Blood & Urine",
      "tat_hours": 24,
      "description": "Comprehensive screening package covering liver, kidney, lipid, and blood health.",
      "tests_count": 7
    }
  ]
}
```

---

### 2.5 Get Package Details (With Included Tests & Parameters)
Retrieves the full breakdown of tests and parameters contained inside a package, preserved in order.

* **Method**: `GET`
* **Endpoint**: `/api/v1/packages/{id}`
* **Headers**: `X-Lab-Api-Key: {your_key}`

#### Example Response:
```json
{
  "success": true,
  "message": "Package details retrieved successfully.",
  "data": {
    "id": 8,
    "name": "Executive Full Body Health Checkup",
    "test_code": "EXEC-FULL",
    "price": 1499.00,
    "sample_type": "Blood & Urine",
    "tat_hours": 24,
    "description": "Comprehensive screening package covering liver, kidney, lipid, and blood health.",
    "tests_count": 2,
    "total_parameters_count": 31,
    "included_tests": [
      {
        "id": 12,
        "name": "Complete Blood Count (CBC)",
        "test_code": "CBC",
        "department": "Hematology",
        "sample_type": "EDTA Whole Blood",
        "parameters_count": 24,
        "parameters": [
          { "name": "Hemoglobin", "unit": "g/dL", "ref_range": "13.0 - 17.0" },
          { "name": "Total WBC Count", "unit": "cells/mcL", "ref_range": "4,000 - 11,000" }
        ]
      },
      {
        "id": 18,
        "name": "Lipid Profile",
        "test_code": "LIPID",
        "department": "Biochemistry",
        "sample_type": "Serum",
        "parameters_count": 7,
        "parameters": [
          { "name": "Total Cholesterol", "unit": "mg/dL", "ref_range": "< 200" },
          { "name": "Triglycerides", "unit": "mg/dL", "ref_range": "< 150" }
        ]
      }
    ]
  }
}
```

---

### 2.6 Submit Online Appointment / Booking
Submits a patient booking from your website into the Pathology software.

* **Method**: `POST`
* **Endpoint**: `/api/v1/bookings`
* **Headers**:
  * `X-Lab-Api-Key: {your_key}`
  * `Content-Type: application/json`

#### Request Body:
```json
{
  "patient_name": "Rahul Verma",
  "patient_phone": "9876543210",
  "patient_email": "rahul.verma@example.com",
  "patient_gender": "male",
  "patient_age": 34,
  "patient_age_unit": "years",
  "collection_type": "home_collection",
  "collection_address": "Flat 402, Green Avenue, Sector 15",
  "preferred_date": "2026-09-25",
  "preferred_time_slot": "08:00 AM - 10:00 AM",
  "branch_id": 1,
  "notes": "Please call 15 minutes before arrival.",
  "items": [
    { "type": "test", "id": 12 },
    { "type": "package", "id": 8 }
  ]
}
```

| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `patient_name` | string | **Yes** | Full name of the patient |
| `patient_phone` | string | **Yes** | 10-digit mobile number |
| `patient_email` | string | No | Email address |
| `patient_gender` | string | No | `male`, `female`, or `other` |
| `patient_age` | integer | No | Patient age |
| `patient_age_unit`| string | No | `years`, `months`, or `days` (default: `years`) |
| `collection_type`| string | **Yes** | `lab_visit` or `home_collection` |
| `collection_address` | string | **Yes (if home_collection)** | Full pickup address |
| `preferred_date` | string | No | Date in `YYYY-MM-DD` format |
| `preferred_time_slot` | string | No | E.g. `08:00 AM - 10:00 AM` |
| `branch_id` | integer | No | Preferred processing lab branch |
| `notes` | string | No | Patient instructions or symptoms |
| `items` | array | **Yes** | Array of selected `{type, id}` objects |

#### Example Response (201 Created):
```json
{
  "success": true,
  "message": "Appointment booked successfully. The lab staff will contact you shortly.",
  "data": {
    "booking_reference": "WB-20260925-A8F2C",
    "status": "pending",
    "patient_name": "Rahul Verma",
    "patient_phone": "9876543210",
    "collection_type": "home_collection",
    "preferred_date": "2026-09-25",
    "preferred_time_slot": "08:00 AM - 10:00 AM",
    "total_amount": 1849.00,
    "items_count": 2,
    "items": [
      {
        "type": "test",
        "id": 12,
        "name": "Complete Blood Count (CBC)",
        "test_code": "CBC",
        "price": 350.00,
        "sample_type": "EDTA Whole Blood"
      },
      {
        "type": "package",
        "id": 8,
        "name": "Executive Full Body Health Checkup",
        "test_code": "EXEC-FULL",
        "price": 1499.00,
        "sample_type": "Blood & Urine"
      }
    ],
    "created_at": "2026-09-24T12:55:00+05:30"
  }
}
```

---

### 2.7 Track Report & Download PDF
Allows patients on your website to check their report status using their **Bill Number** and **Phone Number**, and download the final signed PDF once doctor-approved.

* **Method**: `POST`
* **Endpoint**: `/api/v1/reports/track`
* **Headers**:
  * `X-Lab-Api-Key: {your_key}`
  * `Content-Type: application/json`

#### Request Body:
```json
{
  "bill_number": "INV-2609-0012",
  "phone": "9876543210"
}
```

#### Example Response (When Report is Ready):
```json
{
  "success": true,
  "message": "Report is ready for download.",
  "data": {
    "bill_number": "INV-2609-0012",
    "patient_name": "Rahul Verma",
    "invoice_date": "2026-09-24",
    "expected_report_time": "2026-09-25 14:00",
    "current_stage": "Report Ready",
    "is_ready": true,
    "download_url": "https://your-pathology-domain.com/v/aW52b2ljZV9pZF8xMjg=",
    "tests": [
      { "name": "Complete Blood Count (CBC)", "status": "approved" },
      { "name": "Lipid Profile", "status": "approved" }
    ]
  }
}
```

#### Example Response (When Still Processing):
```json
{
  "success": true,
  "message": "Report is currently in progress.",
  "data": {
    "bill_number": "INV-2609-0012",
    "patient_name": "Rahul Verma",
    "invoice_date": "2026-09-24",
    "expected_report_time": "2026-09-25 14:00",
    "current_stage": "Report Under Analysis",
    "is_ready": false,
    "download_url": null,
    "tests": [
      { "name": "Complete Blood Count (CBC)", "status": "pending" }
    ]
  }
}
```

---

### 2.8 Patient Portal Login (External Website SSO)
Allows patients to log in from your external website (e.g., your WordPress / React landing page) to download reports and view their complete medical history. Upon valid authentication, the API generates a tamper-proof Single Sign-On (SSO) session link that immediately logs the patient in and redirects them directly to their **Patient Portal Dashboard** (`/portal/dashboard`).

* **Method**: `POST`
* **Endpoint**: `/api/v1/patient/login`
* **Headers**:
  * `X-Lab-Api-Key: {your_key}`
  * `Content-Type: application/json`
  * `Accept: application/json`

#### Request Body:
```json
{
  "patient_id": "PAT-1002",
  "phone": "9800300002"
}
```
*Note: You can pass either `patient_id` (e.g. `PAT-1002` or `1002`) OR `bill_number` (e.g. `INV-2609-0012` or Barcode). The `phone` field must match the patient's registered mobile number.*

| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `patient_id` | string | Either this or `bill_number` | Patient ID (e.g., `PAT-1002`) |
| `bill_number`| string | Either this or `patient_id` | Invoice or Bill Number (e.g., `INV-2609-0012`) |
| `phone` | string | **Yes** | 10-digit registered mobile number |

#### Example Response (200 OK):
```json
{
  "success": true,
  "message": "Login verified successfully. Redirecting to patient dashboard...",
  "data": {
    "patient_name": "Sunita Devi",
    "patient_id": "PAT-1002",
    "phone": "9800300002",
    "redirect_url": "https://your-pathology-domain.com/portal/auth/sso/20?expires=1790238870&signature=a985cbcf0a02...",
    "dashboard_url": "https://your-pathology-domain.com/portal/dashboard"
  }
}
```

#### How External Redirection Works:
1. When your external website submits the credentials to `/api/v1/patient/login`, the response returns a signed `redirect_url`.
2. In your frontend JavaScript, simply redirect the browser:
   ```javascript
   window.location.href = result.data.redirect_url;
   ```
3. The patient's browser seamlessly opens the signed URL, authenticates their session, and lands directly on their `/portal/dashboard` where they can view, download, or print all their test reports.

---

## 💻 3. Code Integration Examples

### JavaScript (Fetch API / Next.js / React)
```javascript
const API_BASE = "https://your-pathology-domain.com/api/v1";
const API_KEY = "lab_your_secret_api_key";

// 1. Patient Portal Login & Seamless Dashboard Redirect
async function loginPatient(patientIdOrBill, phone) {
  try {
    const response = await fetch(`${API_BASE}/patient/login`, {
      method: "POST",
      headers: {
        "X-Lab-Api-Key": API_KEY,
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({
        patient_id: patientIdOrBill,
        phone: phone
      })
    });

    const result = await response.json();

    if (result.success && result.data.redirect_url) {
      // 🚀 Redirect patient directly into their authenticated Patient Dashboard
      window.location.href = result.data.redirect_url;
    } else {
      alert(result.message || "Invalid Patient ID / Bill Number or Mobile Number.");
    }
  } catch (error) {
    console.error("Login failed:", error);
    alert("Connection error. Please try again.");
  }
}

// 2. Fetch Tests
async function getTests(search = '') {
  const response = await fetch(`${API_BASE}/tests?search=${encodeURIComponent(search)}`, {
    headers: {
      "X-Lab-Api-Key": API_KEY,
      "Accept": "application/json"
    }
  });
  const result = await response.json();
  return result.data.tests;
}

// 3. Book an Appointment
async function bookAppointment(bookingData) {
  const response = await fetch(`${API_BASE}/bookings`, {
    method: "POST",
    headers: {
      "X-Lab-Api-Key": API_KEY,
      "Content-Type": "application/json",
      "Accept": "application/json"
    },
    body: JSON.stringify(bookingData)
  });
  return await response.json();
}

// 4. Track Report
async function trackReport(billNumber, phone) {
  const response = await fetch(`${API_BASE}/reports/track`, {
    method: "POST",
    headers: {
      "X-Lab-Api-Key": API_KEY,
      "Content-Type": "application/json",
      "Accept": "application/json"
    },
    body: JSON.stringify({ bill_number: billNumber, phone: phone })
  });
  return await response.json();
}
```

---

### Ready-to-Use Embeddable HTML Login Form (Copy & Paste)
You can paste this clean, responsive login card directly onto any page of your external website:

```html
<!-- Patient Login Form Widget -->
<div id="patient-login-card" style="max-width: 420px; margin: 40px auto; padding: 28px; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); font-family: system-ui, sans-serif;">
  <h3 style="margin-top: 0; color: #1e293b; font-size: 20px; font-weight: 700;">Download Lab Reports</h3>
  <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Enter your Patient ID or Bill Number to access your patient dashboard.</p>
  
  <form id="externalPatientLoginForm">
    <div style="margin-bottom: 14px;">
      <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Patient ID or Bill No.</label>
      <input type="text" id="patient_id" placeholder="e.g. PAT-1002 or INV-2609-0012" required
             style="width: 100%; box-sizing: border-box; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="margin-bottom: 20px;">
      <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Registered Mobile Number</label>
      <input type="tel" id="patient_phone" placeholder="10-digit mobile number" required pattern="[0-9]{10}"
             style="width: 100%; box-sizing: border-box; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <button type="submit" id="loginBtn"
            style="width: 100%; padding: 12px; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; transition: background 0.2s;">
      Login & View Dashboard &rarr;
    </button>
    <div id="loginMsg" style="margin-top: 12px; font-size: 13px; text-align: center; display: none;"></div>
  </form>
</div>

<script>
document.getElementById('externalPatientLoginForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('loginBtn');
  const msg = document.getElementById('loginMsg');
  const patientId = document.getElementById('patient_id').value.trim();
  const phone = document.getElementById('patient_phone').value.trim();

  btn.disabled = true;
  btn.innerText = 'Verifying credentials...';
  msg.style.display = 'none';

  try {
    const res = await fetch('https://your-pathology-domain.com/api/v1/patient/login', {
      method: 'POST',
      headers: {
        'X-Lab-Api-Key': 'lab_your_secret_api_key_here',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ patient_id: patientId, phone: phone })
    });

    const data = await res.json();

    if (data.success && data.data && data.data.redirect_url) {
      msg.style.display = 'block';
      msg.style.color = '#16a34a';
      msg.innerText = 'Success! Redirecting to dashboard...';
      // Seamless redirect to authenticated dashboard
      window.location.href = data.data.redirect_url;
    } else {
      msg.style.display = 'block';
      msg.style.color = '#dc2626';
      msg.innerText = data.message || 'Invalid details. Please verify your ID and Mobile.';
      btn.disabled = false;
      btn.innerText = 'Login & View Dashboard \u2192';
    }
  } catch (err) {
    msg.style.display = 'block';
    msg.style.color = '#dc2626';
    msg.innerText = 'Unable to connect to the server. Please check your internet connection.';
    btn.disabled = false;
    btn.innerText = 'Login & View Dashboard \u2192';
  }
});
</script>
```

### PHP / WordPress (`wp_remote_post`)
```php
function pathology_submit_booking($booking_data) {
    $api_url = 'https://your-pathology-domain.com/api/v1/bookings';
    $api_key = 'lab_your_secret_api_key';

    $response = wp_remote_post($api_url, [
        'headers' => [
            'X-Lab-Api-Key' => $api_key,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ],
        'body'    => json_encode($booking_data),
        'timeout' => 20,
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    return json_decode(wp_remote_retrieve_body($response), true);
}
```

---

## 🛡️ 4. HTTP Status Codes & Error Handling

| Status Code | Reason | Resolution |
| :--- | :--- | :--- |
| `200 OK` | Request succeeded | Process data array |
| `201 Created` | Booking successfully created | Show reference code to patient |
| `401 Unauthorized` | Missing or invalid API Key | Check `X-Lab-Api-Key` header |
| `403 Forbidden` | Plan feature not enabled or origin blocked | Check Superadmin plan has `website_api` active |
| `404 Not Found` | Test, package, or bill number not found | Verify IDs / bill number |
| `422 Unprocessable` | Input validation failed | Inspect the `errors` object in the JSON response |
