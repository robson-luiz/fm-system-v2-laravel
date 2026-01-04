<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('two_factor_enabled')->default(false)->after('email_verified_at');
            $table->enum('two_factor_method', ['email', 'sms'])->default('email')->after('two_factor_enabled');
            $table->string('phone_number')->nullable()->after('two_factor_method');
            $table->timestamp('phone_verified_at')->nullable()->after('phone_number');
            $table->timestamp('two_factor_verified_at')->nullable()->after('phone_verified_at');
            $table->integer('two_factor_failed_attempts')->default(0)->after('two_factor_verified_at');
            $table->timestamp('two_factor_locked_until')->nullable()->after('two_factor_failed_attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_enabled',
                'two_factor_method',
                'phone_number',
                'phone_verified_at',
                'two_factor_verified_at',
                'two_factor_failed_attempts',
                'two_factor_locked_until'
            ]);
        });
    }
};