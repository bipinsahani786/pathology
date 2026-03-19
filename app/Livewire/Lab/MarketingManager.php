<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Membership;
use App\Models\Voucher;
use Illuminate\Validation\Rule;

class MarketingManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $activeTab = 'memberships'; // Default Tab

    // ================= MEMBERSHIP FIELDS =================
    public $membership_id, $m_name, $m_price = 0, $m_discount_percentage = 0;
    public $m_validity_days = 365, $m_color_code = '#3b71ca', $m_description, $m_is_active = true;
    public $isMembershipModalOpen = false;

    // ================= VOUCHER FIELDS =================
    public $voucher_id, $v_code, $v_discount_type = 'percentage', $v_discount_value = 0;
    public $v_min_bill_amount = 0, $v_max_discount_amount, $v_valid_from, $v_valid_until;
    public $v_usage_limit, $v_is_active = true;
    public $isVoucherModalOpen = false;

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    // ----------------- MEMBERSHIP LOGIC -----------------
    public function createMembership()
    {
        $this->resetMembershipFields();
        $this->isMembershipModalOpen = true;
    }

    public function editMembership($id)
    {
        $this->resetMembershipFields();
        $membership = Membership::findOrFail($id);

        $this->membership_id = $membership->id;
        $this->m_name = $membership->name;
        $this->m_price = $membership->price;
        $this->m_discount_percentage = $membership->discount_percentage;
        $this->m_validity_days = $membership->validity_days;
        $this->m_color_code = $membership->color_code;
        $this->m_description = $membership->description;
        $this->m_is_active = $membership->is_active;

        $this->isMembershipModalOpen = true;
    }

    public function storeMembership()
    {
        $this->validate([
            'm_name' => 'required|string|max:255',
            'm_price' => 'required|numeric|min:0',
            'm_discount_percentage' => 'required|numeric|min:0|max:100',
            'm_validity_days' => 'required|integer|min:1',
            'm_color_code' => 'required|string|max:20',
        ]);

        Membership::updateOrCreate(
        ['id' => $this->membership_id],
        [
            'company_id' => auth()->user()->company_id,
            'name' => $this->m_name,
            'price' => $this->m_price,
            'discount_percentage' => $this->m_discount_percentage,
            'validity_days' => $this->m_validity_days,
            'color_code' => $this->m_color_code,
            'description' => $this->m_description,
            'is_active' => $this->m_is_active,
        ]
        );

        session()->flash('message', 'Membership saved successfully.');
        $this->isMembershipModalOpen = false;
    }

    public function deleteMembership($id)
    {
        Membership::findOrFail($id)->delete();
        session()->flash('message', 'Membership deleted.');
    }

    public function toggleMembershipStatus($id)
    {
        $m = Membership::findOrFail($id);
        $m->update(['is_active' => !$m->is_active]);
    }

    public function resetMembershipFields()
    {
        $this->reset(['membership_id', 'm_name', 'm_price', 'm_discount_percentage', 'm_validity_days', 'm_color_code', 'm_description', 'm_is_active']);
        $this->resetValidation();
    }

    // ----------------- VOUCHER LOGIC -----------------
    public function createVoucher()
    {
        $this->resetVoucherFields();
        $this->isVoucherModalOpen = true;
    }

    public function editVoucher($id)
    {
        $this->resetVoucherFields();
        $voucher = Voucher::findOrFail($id);

        $this->voucher_id = $voucher->id;
        $this->v_code = $voucher->code;
        $this->v_discount_type = $voucher->discount_type;
        $this->v_discount_value = $voucher->discount_value;
        $this->v_min_bill_amount = $voucher->min_bill_amount;
        $this->v_max_discount_amount = $voucher->max_discount_amount;
        $this->v_valid_from = $voucher->valid_from ? $voucher->valid_from->format('Y-m-d') : null;
        $this->v_valid_until = $voucher->valid_until ? $voucher->valid_until->format('Y-m-d') : null;
        $this->v_usage_limit = $voucher->usage_limit;
        $this->v_is_active = $voucher->is_active;

        $this->isVoucherModalOpen = true;
    }

    public function storeVoucher()
    {
        $this->v_code = strtoupper(trim($this->v_code)); // Ensure code is uppercase

        $this->validate([
            'v_code' => [
                'required', 'string', 'max:50',
                Rule::unique('vouchers', 'code')
                ->where('company_id', auth()->user()->company_id)
                ->ignore($this->voucher_id)
            ],
            'v_discount_type' => 'required|in:percentage,flat',
            'v_discount_value' => 'required|numeric|min:0.01',
            'v_min_bill_amount' => 'nullable|numeric|min:0',
            'v_max_discount_amount' => 'nullable|numeric|min:0',
            'v_valid_from' => 'nullable|date',
            'v_valid_until' => 'nullable|date|after_or_equal:v_valid_from',
            'v_usage_limit' => 'nullable|integer|min:1',
        ]);

        Voucher::updateOrCreate(
        ['id' => $this->voucher_id],
        [
            'company_id' => auth()->user()->company_id,
            'code' => $this->v_code,
            'discount_type' => $this->v_discount_type,
            'discount_value' => $this->v_discount_value,
            'min_bill_amount' => $this->v_min_bill_amount ?? 0,
            'max_discount_amount' => $this->v_max_discount_amount ?: null,
            'valid_from' => $this->v_valid_from ?: null,
            'valid_until' => $this->v_valid_until ?: null,
            'usage_limit' => $this->v_usage_limit ?: null,
            'is_active' => $this->v_is_active,
        ]
        );

        session()->flash('message', 'Promo Code saved successfully.');
        $this->isVoucherModalOpen = false;
    }

    public function deleteVoucher($id)
    {
        Voucher::findOrFail($id)->delete();
        session()->flash('message', 'Voucher deleted.');
    }

    public function toggleVoucherStatus($id)
    {
        $v = Voucher::findOrFail($id);
        $v->update(['is_active' => !$v->is_active]);
    }

    public function resetVoucherFields()
    {
        $this->reset(['voucher_id', 'v_code', 'v_discount_type', 'v_discount_value', 'v_min_bill_amount', 'v_max_discount_amount', 'v_valid_from', 'v_valid_until', 'v_usage_limit', 'v_is_active']);
        $this->resetValidation();
    }

    public function render()
    {
        // Using distinct pagination names so they don't clash on the same page
        $memberships = Membership::orderBy('id', 'desc')->paginate(10, ['*'], 'membershipsPage');
        $vouchers = Voucher::orderBy('id', 'desc')->paginate(10, ['*'], 'vouchersPage');

        return view('livewire.lab.marketing-manager', [
            'memberships' => $memberships,
            'vouchers' => $vouchers,
        ])->layout('layouts.app', ['title' => 'Offers & Marketing']);
    }
}