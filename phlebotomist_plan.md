# Implementation Plan - Phlebotomist (Home Collection) Module

Aapne jo Phlebotomist (home sample collection) ka feature maanga hai, wo pathology lab ke liye ek bahut important growth engine hai. Isko hum properly architect karenge taaki future me mobile app jaisa experience mile.

Yahan ek detailed plan hai ki kya kya changes karne honge aur hum kya features de sakte hain:

## 🚀 Proposed Features (Kya kya de paayenge)

### 1. Phlebotomist Dashboard (Mobile-Friendly)
- **Today's Visits:** Unko apne phone par daily assign kiye hue visits dikhenge (Patient Name, Tests, Address).
- **Navigation & Call:** One-click Google Maps navigation aur Call button.
- **Status Updates:** Sample collect karne ke baad wo directly phone se status "Collected" mark kar payenge.
- **Payment Collection:** Agar bill me balance baaki hai, toh phlebotomist payment (Cash/UPI) collect kar payega aur system me entry daal payega.

### 2. POS & Billing Workflow
- **Home Collection Fee:** Jab POS me `Collection Type = Home Collection` select hoga, toh system automatically "Home Collection Charge" (e.g., ₹200) bill me add kar dega.
- **Address & Scheduling:** POS me address dalne ka option aur collection ka Date/Time slot select karne ka option aayega.
- **Direct Assignment:** Billing ke time hi admin kisi specific phlebotomist ko assign kar sakta hai.

### 3. Admin / Dispatcher Panel
- **Visit Tracking:** Admin ko ek dashboard dikhega jahan saari pending home collections dikhengi.
- **Reassignment:** Agar koi phlebotomist available nahi hai, toh admin kisi aur ko assign kar sakta hai.

### 4. Patient Notifications (Future Scope)
- Jab phlebotomist assign hoga, patient ko SMS/WhatsApp jayega: *"Your home collection is assigned to [Phlebotomist Name] - [Phone Number]"*.

---

## 🛠️ Technical Changes Required (Kya kya change karna hoga)

### Phase 1: Database & Roles (Foundation)
#### [NEW] Role & Profile
- `phlebotomist` role create karna hoga via Spatie Permissions.
- `phlebotomist_profiles` table create karni hogi:
  - `user_id`, `company_id`
  - `vehicle_number` (optional)
  - `commission_per_visit` (agar unko per visit paisa milta hai)

#### [NEW] Home Collections Table
- `home_collections` table banani hogi:
  - `invoice_id` (Bill reference)
  - `patient_id`
  - `phlebotomist_id` (Nullable, baad me assign karne ke liye)
  - `collection_address`
  - `scheduled_at` (Kis din aur kitne baje jana hai)
  - `collected_at` (Actual time of collection)
  - `status` (Pending, Assigned, Collected, Cancelled)

### Phase 2: POS Modifications
#### [MODIFY] `PosManager.php` & `PosEditManager.php`
- Jab `collection_type === 'Home Collection'` ho, toh UI me additional fields show karni hongi:
  - `collection_address`
  - `scheduled_date` & `scheduled_time`
  - `phlebotomist_id` (Dropdown of active phlebotomists)
- Invoice save hote time, `home_collections` table me entry create karni hogi.

#### [MODIFY] `pos-manager.blade.php` & `pos-edit-manager.blade.php`
- UI update for dynamic fields based on collection type.

### Phase 3: Admin Screens
#### [NEW] Phlebotomist Management
- List, Add, Edit Phlebotomists (Similar to Doctors/Agents).

#### [NEW] Home Visits Management
- Ek naya page: `/admin/home-visits` jahan admin saare visits dekh sake, assign kar sake aur status update kar sake.

### Phase 4: Phlebotomist Portal
#### [NEW] Phlebotomist Controller & Views
- `Phlebotomist/DashboardController.php`
- Mobile-responsive view jahan phlebotomist login karke apne tasks dekh sake.

---

## 🙋‍♂️ User Review Required (Future)
**Aapke liye open questions (Jab feature banayenge):**
1. **Home Collection Fee:** Kya hum home collection par extra charge lagate hain? (e.g., Flat ₹150 or based on distance). Kya yeh POS me automatic add hona chahiye?
2. **Phlebotomist Commission:** Kya Phlebotomists ko per visit commission ya salary milti hai?
