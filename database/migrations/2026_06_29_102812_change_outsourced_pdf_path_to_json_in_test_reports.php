<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, grab the old data before changing the column type
        $reports = DB::table('test_reports')->whereNotNull('outsourced_pdf_path')->get(['id', 'outsourced_pdf_path']);

        Schema::table('test_reports', function (Blueprint $table) {
            $table->text('outsourced_pdf_path')->nullable()->change();
        });

        // Convert old string paths into JSON arrays to preserve backward compatibility
        foreach ($reports as $report) {
            $path = $report->outsourced_pdf_path;
            // Only update if it's not already a JSON array (checking if it starts with '[')
            if ($path && !str_starts_with(trim($path), '[')) {
                DB::table('test_reports')
                    ->where('id', $report->id)
                    ->update(['outsourced_pdf_path' => json_encode([$path])]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $reports = DB::table('test_reports')->whereNotNull('outsourced_pdf_path')->get(['id', 'outsourced_pdf_path']);

        foreach ($reports as $report) {
            $paths = json_decode($report->outsourced_pdf_path, true);
            $singlePath = is_array($paths) && count($paths) > 0 ? $paths[0] : null;
            
            DB::table('test_reports')
                ->where('id', $report->id)
                ->update(['outsourced_pdf_path' => $singlePath]);
        }

        Schema::table('test_reports', function (Blueprint $table) {
            $table->string('outsourced_pdf_path')->nullable()->change();
        });
    }
};
