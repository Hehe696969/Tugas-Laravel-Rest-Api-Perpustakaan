<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedUser()
    {
        return $this->actingAs(User::factory()->create(), 'sanctum');
    }

    private function bookData(): array
    {
        return [
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'publisher' => 'Bentang Pustaka',
            'isbn' => '9789793062792',
            'published_year' => 2005,
            'stock' => 10,
            'description' => 'Novel tentang perjuangan anak-anak Belitung.',
        ];
    }

    public function test_user_can_get_books(): void
    {
        $this->authenticatedUser();

        Book::create($this->bookData());

        $response = $this->getJson('/api/books');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'author', 'isbn'],
                ],
            ]);
    }

    public function test_user_can_create_book(): void
    {
        $this->authenticatedUser();

        $response = $this->postJson('/api/books', $this->bookData());

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Laskar Pelangi');

        $this->assertDatabaseHas('books', [
            'isbn' => '9789793062792',
        ]);
    }

    public function test_user_can_show_book(): void
    {
        $this->authenticatedUser();

        $book = Book::create($this->bookData());

        $response = $this->getJson('/api/books/'.$book->id);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Laskar Pelangi');
    }

    public function test_user_can_update_book(): void
    {
        $this->authenticatedUser();

        $book = Book::create($this->bookData());

        $response = $this->putJson('/api/books/'.$book->id, [
            'title' => 'Laskar Pelangi Edisi Revisi',
            'author' => 'Andrea Hirata',
            'isbn' => '9789793062792',
            'published_year' => 2005,
            'stock' => 15,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Laskar Pelangi Edisi Revisi');
    }

    public function test_user_can_delete_book(): void
    {
        $this->authenticatedUser();

        $book = Book::create($this->bookData());

        $response = $this->deleteJson('/api/books/'.$book->id);

        $response->assertOk();

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
}