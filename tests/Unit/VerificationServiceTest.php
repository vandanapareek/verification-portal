<?php

namespace Tests\Unit;

use App\Models\VerificationResult;
use App\Services\VerificationService;
use App\Models\User;
use Tests\TestCase;

class VerificationServiceTest extends TestCase
{

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    /**
     * Test case for verifying a valid file.
     *
     * @return void
     */

    public function testVerifiedFileResult()
    {
        // Create a user
        $user = User::factory()->create();

        // Authenticate the user
        $this->actingAs($user);
        
        // Create an instance of the VerificationService, passing the mock user object
        $verificationService = new VerificationService();

        // Prepare sample file data
        $fileData = [
            "data" => [
                "id" => "63c79bd9303530645d1cca00", 
                "name" => "Certificate of Completion", 
                "recipient" => [
                    "name" => "Marty McFly", 
                    "email" => "marty.mcfly@gmail.com" 
                ], 
                "issuer" => [
                        "name" => "Accredify", 
                        "identityProof" => [
                            "type" => "DNS-DID", 
                            "key" => "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller", 
                            "location" => "ropstore.accredify.io" 
                        ] 
                    ], 
                "issued" => "2022-12-23T00:00:00+08:00" 
                ], 
            "signature" => [
                            "type" => "SHA3MerkleProof", 
                            "targetHash" => "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e" 
                            ] 
        ]; 


        // Call the verifyFile method on the VerificationService
        $result = $verificationService->verifyFile($fileData);
        // var_dump($result['status']);
        $this->assertEquals(VerificationResult::RESULT_VERIFIED, $result['status']);
    }


    /**
     * Test case for verifying a file with an invalid recipient.
     *
     * @return void
     */

    public function testInvalidRecipient()
    {
        // Create a user
        $user = User::factory()->create();

        // Authenticate the user
        $this->actingAs($user);
        
        // Create an instance of the VerificationService, passing the mock user object
        $verificationService = new VerificationService(auth()->user());

        $fileData = [
          "data" => [
                "id" => "63c79bd9303530645d1cca00", 
                "name" => "Certificate of Completion", 
                "recipient" => [
                   "name1" => "Marty McFly", 
                   "email" => "marty.mcfly@gmail.com" 
                ], 
                "issuer" => [
                      "name" => "Accredify", 
                      "identityProof" => [
                         "type" => "DNS-DID", 
                         "key" => "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller", 
                         "location" => "ropstore.accredify.io" 
                      ] 
                   ], 
                "issued" => "2022-12-23T00:00:00+08:00" 
             ], 
          "signature" => [
                            "type" => "SHA3MerkleProof", 
                            "targetHash" => "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e" 
                         ] 
       ]; 


        // Call the verifyFile method on the VerificationService
        $result = $verificationService->verifyFile($fileData);
        //var_dump($result['status']);
        $this->assertEquals(VerificationResult::RESULT_INVALID_RECIPIENT, $result['status']);
    }

    /**
     * Test case for verifying a file with an invalid issuer.
     *
     * @return void
     */

    public function testInvalidIssuer()
    {
        // Create a user
        $user = User::factory()->create();

        // Authenticate the user
        $this->actingAs($user);
        
        // Create an instance of the VerificationService, passing the mock user object
        $verificationService = new VerificationService(auth()->user());

        $fileData = [
          "data" => [
                "id" => "63c79bd9303530645d1cca00", 
                "name" => "Certificate of Completion", 
                "recipient" => [
                   "name" => "Marty McFly", 
                   "email" => "marty.mcfly@gmail.com" 
                ], 
                "issuer" => [
                      "name" => "Accredify", 
                      "identityProof" => [
                         "type1" => "DNS-DID", 
                         "key" => "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller", 
                         "location" => "ropstore.accredify.io" 
                      ] 
                   ], 
                "issued" => "2022-12-23T00:00:00+08:00" 
             ], 
          "signature" => [
                            "type" => "SHA3MerkleProof", 
                            "targetHash" => "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e" 
                         ] 
       ]; 


        // Call the verifyFile method on the VerificationService
        $result = $verificationService->verifyFile($fileData);
        //var_dump($result['status']);
        $this->assertEquals(VerificationResult::RESULT_INVALID_ISSUER, $result['status']);
    }


    /**
     * Test case for verifying a file with a valid signature.
     *
     * @return void
     */

    public function testValidSignature()
    {
        // Create a user
        $user = User::factory()->create();

        // Authenticate the user
        $this->actingAs($user);
        
        // Create an instance of the VerificationService, passing the mock user object
        $verificationService = new VerificationService(auth()->user());

        // Create sample file data with a valid signature
        $fileData = [
            "data" => [
                  "id" => "63c79bd9303530645d1cca00", 
                  "name" => "Certificate of Completion", 
                  "recipient" => [
                     "name" => "Marty McFly", 
                     "email" => "marty.mcfly@gmail.com" 
                  ], 
                  "issuer" => [
                        "name" => "Accredify", 
                        "identityProof" => [
                           "type" => "DNS-DID", 
                           "key" => "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller", 
                           "location" => "ropstore.accredify.io" 
                        ] 
                     ], 
                  "issued" => "2022-12-23T00:00:00+08:00" 
               ], 
            "signature" => [
                              "type" => "SHA3MerkleProof", 
                              "targetHash" => "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e" 
            ] 
         ]; 

      // Call the verifyFile method on the VerificationService
      $result = $verificationService->verifyFile($fileData);
      //var_dump($result['status']);
      $this->assertEquals(VerificationResult::RESULT_VERIFIED, $result['status']);
    }

    /**
     * Test case for verifying a file with a invalid signature.
     *
     * @return void
     */

    public function testInvalidSignature()
    {
        // Create a user
        $user = User::factory()->create();

        // Authenticate the user
        $this->actingAs($user);
        
        // Create an instance of the VerificationService, passing the mock user object
        $verificationService = new VerificationService(auth()->user());

        // Create sample file data with a valid signature
        $fileData = [
            "data" => [
                  "id" => "63c79bd9303530645d1cca00", 
                  "name" => "Certificate of Completion", 
                  "recipient" => [
                     "name" => "Marty McFly", 
                     "email" => "marty.mcfly@gmail.com" 
                  ], 
                  "issuer" => [
                        "name" => "Accredify123", 
                        "identityProof" => [
                           "type" => "DNS-DID", 
                           "key" => "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller", 
                           "location" => "ropstore.accredify.io" 
                        ] 
                     ], 
                  "issued" => "2022-12-23T00:00:00+08:00" 
               ], 
            "signature" => [
                              "type" => "SHA3MerkleProof", 
                              "targetHash" => "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e" 
            ] 
         ]; 

      // Call the verifyFile method on the VerificationService
      $result = $verificationService->verifyFile($fileData);
      //var_dump($result['status']);
      $this->assertEquals(VerificationResult::RESULT_INVALID_SIGNATURE, $result['status']);
    }
}
