<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('filters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Category::class);
            $table->integer('expiration_date');
            $table->foreignIdFor(Order::class);
            $table->foreignIdFor(User::class);
            $table->enum('status', ['not_expired', 'be_changed', 'expired'])->default('not_expired');
            $table->timestamp('ordered_at');
            $table->timestamp('changed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filters');
    }
};
