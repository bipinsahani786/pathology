<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'landing_subtitle')) {
                $table->string('landing_subtitle')->nullable()->after('features');
            }
            if (!Schema::hasColumn('plans', 'landing_features')) {
                $table->json('landing_features')->nullable()->after('landing_subtitle');
            }
            if (!Schema::hasColumn('plans', 'landing_badge')) {
                $table->string('landing_badge')->nullable()->after('landing_features');
            }
            if (!Schema::hasColumn('plans', 'landing_cta_text')) {
                $table->string('landing_cta_text')->default('Get Started')->after('landing_badge');
            }
            if (!Schema::hasColumn('plans', 'landing_sort_order')) {
                $table->integer('landing_sort_order')->default(0)->after('landing_cta_text');
            }
            if (!Schema::hasColumn('plans', 'show_on_landing')) {
                $table->boolean('show_on_landing')->default(true)->after('landing_sort_order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $columns = ['landing_subtitle', 'landing_features', 'landing_badge', 'landing_cta_text', 'landing_sort_order', 'show_on_landing'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('plans', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
