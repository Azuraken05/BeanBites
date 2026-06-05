<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Adds soft deletes tracking timestamp capability natively to Laravel
            $table->softDeletes(); 
            
            // Tracks context remarks and the account execution ID for future report listings
            $table->text('delete_remarks')->nullable()->after('image_path');
            $table->foreignId('deleted_by_user_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['deleted_by_user_id']);
            $table->dropColumn(['delete_remarks', 'deleted_by_user_id']);
        });
    }
};