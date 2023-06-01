<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    /**
     * Test unauthorized user access to verification endpoint.
     */
    public function testUnauthorizedUser()
    {
        $this->withoutMiddleware();

        // Make the request to verify
        $response = $this->withHeaders([
            'Accept' => '*/*',
        ])->json('POST', '/api/verify');

        // Assert that the response has a status code of 401
        $response->assertStatus(401);
    }

    /**
     * Test verifying without a file parameter.
     */
    public function testMissingFile()
    {
        // Create a user
        $user = User::factory()->create();

        // Authenticate the user
        $this->actingAs($user);
        
        // Create the request without a file parameter
        $request = $this->actingAs($user)->postJson('/api/verify', []);
        
        // Assert that the response has a status code of 400
        $request->assertStatus(400);
        
        // Assert that the response contains the expected error message
        $request->assertJson([
            'success' => false,
            'error' => 'Invalid request. Missing file parameter.',
        ]);
    }
 

    /**
     * Test verifying when the file exceeds the size limit.
     */
    public function testFileSize()
    {
        $this->withoutMiddleware();

        // Create a fake user to simulate authentication
        $user = User::factory()->create();

        // Create a fake file that exceeds the size limit
        $file = UploadedFile::fake()->create('test_file.txt', 3000); // Adjust the file size as per your requirements

        // Make the request to /api/verify without attaching the file
        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->postJson('/api/verify');

        // Assert that the response has a status code of 400 (Bad Request)
        $response->assertStatus(400);

        // Assert that the response contains the expected error message for the missing file parameter
        $response->assertJson([
            'success' => false,
            'error' => 'Invalid request. Missing file parameter.',
        ]);
    }

    
    /**
     * Test verifying with JSON files.
     */
    public function testRequestFileType()
    {
        $this->withoutMiddleware();
    
        // Create a fake user to simulate authentication
        $user = User::factory()->create();
    
        // Create a fake JSON file
        $fileData = ['example' => 'data'];
        $file = UploadedFile::fake()->create('test_file.json', json_encode($fileData));
    
        // Make the request to /api/verify with the JSON file
        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->postJson('/api/verify', ['file' => $fileData]);
    
        // Assert that the response has a status code of 200 (OK)
        $response->assertStatus(400);
        // Add any other assertions you need for a successful verification
    
        // Optionally, you can also assert that the response does not contain any validation errors
        $response->assertJsonMissingValidationErrors();
    }
    
}
