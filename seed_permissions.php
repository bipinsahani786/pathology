<?php
use Spatie\Permission\Models\Permission;

$modules = [
    'patients', 'doctors', 'agents', 'lab_tests', 'test_packages', 
    'departments', 'invoices', 'reports', 'settlements', 'branches', 
    'collection_centers', 'payment_modes', 'marketing', 'staff_roles', 'settings',
    'pos', 'wallets'
];
$actions = ['view', 'create', 'edit', 'delete'];

foreach ($modules as $module) {
    foreach ($actions as $action) {
        Permission::firstOrCreate(['name' => "$action $module"]);
    }
}
echo "Done seeding permissions.";
