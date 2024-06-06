<?php

use App\Models\Employer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('phone');
            $table->string('password');
            $table->string('email')->unique();
            $table->timestamps();
        });

        Employer::create([
            'name' => 'admin',
            'email' =>'admin@gmail.com',
            'phone' =>'12345678',
            'password' =>Hash::make('12345678')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employers');
    }
};
