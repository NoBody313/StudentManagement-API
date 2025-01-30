<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_students()
    {
        // Arrange: Buat beberapa data siswa
        Student::factory()->count(3)->create();

        // Act: Panggil endpoint GET /api/students
        $response = $this->getJson('/api/students');

        // Assert: Pastikan data siswa muncul
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_create_a_student()
    {
        // Arrange: Data siswa
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'date_of_birth' => '2000-01-01',
            'phone' => '08123456789',
        ];

        // Act: Kirim request POST /api/students
        $response = $this->postJson('/api/students', $data);

        // Assert: Pastikan data berhasil dibuat
        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'John Doe']);
        $this->assertDatabaseHas('students', ['email' => 'john.doe@example.com']);
    }

    /** @test */
    public function it_can_show_a_student()
    {
        // Arrange: Buat data siswa
        $student = Student::factory()->create();

        // Act: Panggil endpoint GET /api/students/{id}
        $response = $this->getJson('/api/students/' . $student->id);

        // Assert: Pastikan data siswa muncul
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $student->id]);
    }

    /** @test */
    public function it_can_update_a_student()
    {
        // Arrange: Buat data siswa
        $student = Student::factory()->create();

        // Data update
        $data = ['name' => 'Jane Doe'];

        // Act: Kirim request PUT /api/students/{id}
        $response = $this->putJson('/api/students/' . $student->id, $data);

        // Assert: Pastikan data berhasil diupdate
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Jane Doe']);
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Jane Doe']);
    }

    /** @test */
    public function it_can_delete_a_student()
    {
        // Arrange: Buat data siswa
        $student = Student::factory()->create();

        // Act: Kirim request DELETE /api/students/{id}
        $response = $this->deleteJson('/api/students/' . $student->id);

        // Assert: Pastikan data berhasil dihapus
        $response->assertStatus(200)
            ->assertJson(['message' => 'Student deleted']);
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }
}
