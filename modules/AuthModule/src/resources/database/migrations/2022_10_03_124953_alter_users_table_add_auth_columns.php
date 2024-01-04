<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')
                ->nullable()
                ->change();
            $table->renameColumn('name', 'full_name');
            $table->string('email')
                ->nullable()
                ->change();
            $table->text('description')
                ->nullable();
            $table->date('birthdate')
                ->nullable();
            $table->enum('gender', ['male', 'female'])
                ->nullable();
            $table->string('phone_number', 30)
                ->nullable();
            $table->string('email_verification_code', 20)
                ->nullable();
            $table->string('phone_number_verification_code', 20)
                ->nullable();
            $table->timestamp('phone_number_verified_at')
                ->nullable();
            $table->string('primary_username', 12)
                ->default('email')
                ->nullable();
            $table->timestamp('onboarded_at')
                ->nullable();
            $table->timestamp('blocked_at')
                ->nullable();
            $table->unique('phone_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('full_name', 'name');
            $table->string('email')
                ->change();
            $table->dropUnique(['phone_number']);
            $table->dropColumn(
                'description',
                'birthdate',
                'gender',
                'phone_number',
                'email_verification_code',
                'phone_number_verification_code',
                'phone_number_verified_at',
                'primary_username',
                'onboarded_at',
                'blocked_at',
            );
        });
    }
};
