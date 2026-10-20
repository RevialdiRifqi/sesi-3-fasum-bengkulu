<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fasilitas', function (Blueprint $t) {
            $t->id();
            $t->string('nama');
            $t->enum('kategori', ['sekolah', 'rumah_sakit', 'pemerintahan']);
            $t->string('alamat')->nullable();
            $t->string('telepon', 30)->nullable();
            $t->decimal('latitude', 10, 7);
            $t->decimal('longitude', 10, 7);
            $t->timestamps();
            $t->index('kategori');
        });
    }
    public function down(): void { Schema::dropIfExists('fasilitas'); }
};
