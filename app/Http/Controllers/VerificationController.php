<?php
/**
 * VerificationController
 * 
 * This controller handles the verification of files using the VerificationService.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Contracts\VerificationServiceInterface;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Verification service instance.
     *
     * @var VerificationServiceInterface
     */
    protected $verificationService;

    /**
     * New VerificationController instance.
     *
     * @param  VerificationServiceInterface  $verificationService
     * @return void
     */
    public function __construct(VerificationServiceInterface $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * Verify a file.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
            // Check if user is authenticated
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            // Check if the request has a file parameter
            if (!$request->hasFile('file')) {
            $response = [
                'success' => false,
                'error' => 'Invalid request. Missing file parameter.',
            ];
            return response()->json($response, JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            // Validate the request
            $validatedData = $request->validate([
                'file' => 'required|file|max:2048|mimes:json',
            ]);

            // Get the file data
            $file = $request->file('file');
            $fileData = json_decode($file->get(),true);

            // Perform verification
            $result = $this->verificationService->verifyFile($fileData);
            
            // Prepare the response
            $response = [
                'data' => [
                    'issuer' => $result['issuer'],
                    'result' => $result['status'],
                ],
            ];

            // Return the verification result
            return response()->json($response);
            
        }catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            $errors = $e->validator->errors()->all();
            $response = [
                'success' => false,
                'error' => 'Validation failed: ' . implode(', ', $errors),
            ];
            return response()->json($response, JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Handle any other exceptions
            $response = [
                'success' => false,
                'error' => 'An error occurred while processing the request: ' . $e->getMessage(),
            ];
            return response()->json($response, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
