<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('languages')) {
            DB::table('languages')->insertOrIgnore([
                'id' => 42,
                'name' => 'Khmer',
                'locale' => 'km',
            ]);
        }
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('languages')) {
            DB::table('languages')->where('locale', 'km')->delete();
        }
    }
};
