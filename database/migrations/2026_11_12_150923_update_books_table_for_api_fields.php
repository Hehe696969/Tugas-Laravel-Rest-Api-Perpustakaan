<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['year', 'category']);

            $table->string('isbn', 20)->unique();
            $table->unsignedSmallInteger('published_year');
            $table->unsignedInteger('stock')->default(0);
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropUnique(['isbn']);
            $table->dropColumn([
                'isbn',
                'published_year',
                'stock',
                'description'
            ]);

            $table->year('year')->nullable();
            $table->string('category')->nullable();
        });
    }
};
