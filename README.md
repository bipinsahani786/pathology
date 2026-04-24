# Pathology SaaS - Multi-Tenant Lab Management System

A premium, state-of-the-art Pathology Lab Management System (LMS) built with Laravel 11, Livewire 3, and Bootstrap 5. This system is designed as a SaaS platform allowing multiple labs to manage their operations, from POS billing and inventory to automated report generation and partner settlements.

---

## 🌟 Key Features

### 🏢 Multi-Tenant SaaS Architecture
- **Super Admin Dashboard**: Manage lab registrations, subscription plans, and global settings.
- **Dynamic Branding**: Labs can customize their own logos, favicons, brand colors, and PDF letterheads.
- **Subscription Engine**: Built-in trial management and plan-based feature restrictions.

### 🔬 Core Lab Operations
- **Advanced POS Billing**: Quick billing with support for vouchers, memberships, and multiple payment modes.
- **Smart Report Entry**: Age and Gender-specific reference range matching with automated formula calculations.
- **Selective Printing**: Ability to select specific tests from an invoice for customized report generation.
- **Inventory Management**: Comprehensive system to track Suppliers, Items, Stock Levels, Purchases, and Issuances.

### 🤝 Partner & Referral Ecosystem
- **Partner Portal**: Dedicated login for Doctors, Referral Agents, and Collection Centers.
- **Commission Tracking**: Automated settlement logic for partner commissions with wallet management.
- **Multi-Branch Support**: Manage multiple branches under a single lab entity with restricted access controls.

### 👤 Patient Experience
- **Patient Portal**: Mobile-friendly dashboard for patients to view diagnostic history and download reports.
- **WhatsApp Integration**: One-click sharing of invoices and reports directly to patients' WhatsApp.
- **Membership Cards**: Dynamic QR-code enabled membership cards for returning patients.

### 🛠 Technical Excellence
- **Cloud Storage**: Seamless integration with Cloudflare R2 for persistent PDF storage.
- **Performance**: Redis-backed session and cache management for high-speed interactions.
- **Security**: Robust RBAC (Role-Based Access Control) using Spatie Permission.
- **Audit Logs**: Comprehensive tracking of all critical financial and medical changes.

---

## 🚀 Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Livewire 3, Alpine.js, Bootstrap 5
- **Database**: MariaDB / MySQL
- **Caching**: Redis
- **Storage**: Cloudflare R2 (S3 Compatible)
- **PDF Engine**: DomPDF
- **Real-time**: Livewire SPA Mode (wire:navigate)

---

## 📦 Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL / MariaDB
- Redis (Optional but recommended)

### Step-by-Step Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-repo/pathology.git
   cd pathology
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configure your Database, Redis, and R2 credentials in the `.env` file.*

4. **Database Migration & Seeding**
   ```bash
   php artisan migrate --seed
   ```

5. **Start the Development Server**
   ```bash
   php artisan serve
   ```

---

## 📁 Key Modules

### 1. Administration (Admin)
- **Lab Manager**: Activate/Deactivate lab accounts.
- **Plan Manager**: Create and price SaaS subscription tiers.
- **Global Test Library**: Master database of tests to sync across new labs.
- **Audit Logs**: Track global system activity.

### 2. Lab Management (Lab)
- **Dashboard**: Real-time analytics on revenue and patient flow.
- **Billing**: POS system with barcode support.
- **Report Center**: Process samples, enter results, and approve reports.
- **Marketing**: Manage Promo Codes and Health Cards.
- **Staff**: Manage lab technicians and receptionists.

### 3. Inventory (Lab)
- **Stock Tracking**: Automatic deduction of reagents on test completion (Planned).
- **Suppliers**: Manage vendor profiles and contact details.

### 4. Partner Portal
- **Settlements**: View earned commissions and payout history.
- **Patient Tracking**: Monitor status of referred patients.

### 5. Patient Portal
- **Digital Records**: Secure access to all past and present diagnostic reports.

---

## 🛡 Security & Permissions

The system uses a granular permission model:
- `super_admin`: Full system control.
- `lab_admin`: Full control over a specific lab.
- `staff`: Billing and report entry access.
- `doctor` / `agent`: Referral tracking access.
- `patient`: Access to personal medical records.

---

## 📄 License
Custom License - All Rights Reserved.

---

*Developed with ❤️ for the healthcare industry.*
