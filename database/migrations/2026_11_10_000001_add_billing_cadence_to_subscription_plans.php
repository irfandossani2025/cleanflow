<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->after('monthly_price');
            $table->string('billing_interval', 12)->default('month')->after('price');
            $table->unsignedSmallInteger('billing_interval_count')->default(1)->after('billing_interval');
            $table->decimal('annual_discount_percent', 5, 2)->nullable()->after('billing_interval_count');
        });

        DB::table('subscription_plans')->whereNotNull('monthly_price')->update([
            'price' => DB::raw('monthly_price'),
            'billing_interval' => 'month',
            'billing_interval_count' => 1,
        ]);
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['price', 'billing_interval', 'billing_interval_count', 'annual_discount_percent']);
        });
    }
};
