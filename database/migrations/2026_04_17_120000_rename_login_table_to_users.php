<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supporte les bases existantes qui avaient encore la table `login`.
        if (Schema::hasTable('login') && !Schema::hasTable('users')) {
            Schema::rename('login', 'users');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && !Schema::hasTable('login')) {
            Schema::rename('users', 'login');
        }
    }
};

