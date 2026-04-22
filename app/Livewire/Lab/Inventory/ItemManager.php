<?php

namespace App\Livewire\Lab\Inventory;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use Livewire\Component;
use Livewire\WithPagination;

class ItemManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Item fields
    public $name, $category_id, $unit = 'pcs', $min_stock_level = 0, $description, $barcode, $item_id;
    public $is_active = true;
    
    // Category fields
    public $category_name, $edit_category_id;
    
    public $searchTerm = '';
    public $isModalOpen = false;
    public $isCategoryModalOpen = false;
    public $activeTab = 'items'; // items, categories

    public function render()
    {
        $items = InventoryItem::with('category')
            ->where('company_id', auth()->user()->company_id)
            ->where('name', 'ilike', '%' . $this->searchTerm . '%')
            ->orderBy('name')
            ->paginate(15);

        $categories = InventoryCategory::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();

        return view('livewire.lab.inventory.item-manager', [
            'items' => $items,
            'categories' => $categories
        ])->layout('layouts.app');
    }

    // ITEM METHODS
    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $item = InventoryItem::where('company_id', auth()->user()->company_id)->findOrFail($id);
        
        $this->item_id = $id;
        $this->name = $item->name;
        $this->category_id = $item->category_id;
        $this->unit = $item->unit;
        $this->min_stock_level = $item->min_stock_level;
        $this->description = $item->description;
        $this->barcode = $item->barcode;
        $this->is_active = $item->is_active;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:inventory_categories,id',
            'unit' => 'required|string|max:20',
        ]);

        InventoryItem::updateOrCreate(
            ['id' => $this->item_id],
            [
                'company_id' => auth()->user()->company_id,
                'category_id' => $this->category_id,
                'name' => $this->name,
                'unit' => $this->unit,
                'min_stock_level' => $this->min_stock_level,
                'description' => $this->description,
                'barcode' => $this->barcode,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('success', $this->item_id ? 'Item updated.' : 'New item created.');
        $this->isModalOpen = false;
        $this->resetFields();
    }

    // CATEGORY METHODS
    public function createCategory()
    {
        $this->resetCategoryFields();
        $this->isCategoryModalOpen = true;
    }

    public function editCategory($id)
    {
        $cat = InventoryCategory::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $this->edit_category_id = $id;
        $this->category_name = $cat->name;
        $this->isCategoryModalOpen = true;
    }

    public function storeCategory()
    {
        $this->validate([
            'category_name' => 'required|string|max:255',
        ]);

        InventoryCategory::updateOrCreate(
            ['id' => $this->edit_category_id],
            [
                'company_id' => auth()->user()->company_id,
                'name' => $this->category_name,
            ]
        );

        session()->flash('success', $this->edit_category_id ? 'Category updated.' : 'New category created.');
        $this->isCategoryModalOpen = false;
        $this->resetCategoryFields();
    }

    public function resetFields()
    {
        $this->reset(['name', 'category_id', 'unit', 'min_stock_level', 'description', 'barcode', 'item_id', 'is_active']);
        $this->resetValidation();
    }

    public function resetCategoryFields()
    {
        $this->reset(['category_name', 'edit_category_id']);
        $this->resetValidation();
    }
    
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->isCategoryModalOpen = false;
        $this->resetFields();
        $this->resetCategoryFields();
    }
}
