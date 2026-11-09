<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->unique(); $table->string('email')->nullable(); $table->string('phone')->nullable(); $table->text('address')->nullable();
            $table->string('country', 2)->nullable(); $table->string('timezone')->default('UTC'); $table->string('locale')->default('en'); $table->string('currency', 3)->default('USD');
            $table->string('logo_path')->nullable(); $table->text('service_area')->nullable(); $table->text('quote_terms')->nullable(); $table->unsignedSmallInteger('quote_expiry_days')->default(14);
            $table->json('working_hours')->nullable(); $table->text('quote_email_template')->nullable(); $table->boolean('onboarding_completed')->default(false); $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) { $table->foreign('business_id')->references('id')->on('businesses')->nullOnDelete(); });
        Schema::create('services', function (Blueprint $table) {
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->string('name'); $table->text('description')->nullable();
            $table->unsignedInteger('duration_minutes')->default(60); $table->decimal('base_price', 12, 2)->default(0); $table->boolean('active')->default(true); $table->timestamps();
        });
        Schema::create('service_extras', function (Blueprint $table) {
            $table->id(); $table->foreignId('service_id')->constrained()->cascadeOnDelete(); $table->string('name'); $table->decimal('price', 12, 2)->default(0); $table->unsignedInteger('duration_minutes')->default(0); $table->boolean('active')->default(true); $table->timestamps();
        });
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->string('name'); $table->string('email')->nullable()->index(); $table->string('phone')->nullable(); $table->text('address')->nullable(); $table->boolean('marketing_opt_out')->default(false); $table->timestamps();
        });
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete(); $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('new')->index(); $table->string('source')->default('manual')->index(); $table->string('property_type')->nullable(); $table->text('property_address')->nullable();
            $table->unsignedInteger('bedrooms')->nullable(); $table->unsignedInteger('bathrooms')->nullable(); $table->date('preferred_date')->nullable(); $table->text('notes')->nullable(); $table->timestamp('received_at')->useCurrent(); $table->timestamp('follow_up_at')->nullable()->index(); $table->timestamps();
        });
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id(); $table->foreignId('business_id')->constrained()->cascadeOnDelete(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $table->morphs('subject'); $table->string('action'); $table->json('properties')->nullable(); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('activity_logs'); Schema::dropIfExists('enquiries'); Schema::dropIfExists('customers'); Schema::dropIfExists('service_extras'); Schema::dropIfExists('services'); Schema::table('users', fn (Blueprint $table) => $table->dropForeign(['business_id'])); Schema::dropIfExists('businesses'); }
};
