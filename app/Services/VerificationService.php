<?php
/**
 * Class VerificationService
 * 
 * Service class for verifying file data.
 */

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Contracts\VerificationServiceInterface;
use App\Models\VerificationResult;
use Illuminate\Support\Facades\Log;


class VerificationService implements VerificationServiceInterface
{
    private $verificationResultModel;

    /**
     * Verifies the file data and returns the verification result.
     * 
     * @param array $fileData The file data to be verified.
     * @return array The verification result containing the issuer and status.
     */
    public function verifyFile(array $fileData): array
    {
        $verificationResult = $this->performVerification($fileData);
        $this->saveVerificationResult($verificationResult);

        return [
            'issuer' => $verificationResult['issuer'],
            'status' => $verificationResult['status'],
        ];
    }

    /**
     * Performs the verification process on the file data.
     * 
     * @param array $fileData The file data to be verified.
     * @return array The verification result containing the issuer and status.
     */
    private function performVerification(array $fileData): array
    {
        $validRecipient = $this->validateRecipient($fileData);
        if (!$validRecipient) {
            return [
                'issuer' => '',
                'status' => VerificationResult::RESULT_INVALID_RECIPIENT,
            ];
        }

        $validIssuer = $this->validateIssuer($fileData);
        if (!$validIssuer) {
            return [
                'issuer' => '',
                'status' => VerificationResult::RESULT_INVALID_ISSUER,
            ];
        }

        $validSignature = $this->validateSignature($fileData);
        if (!$validSignature) {
            return [
                'issuer' => '',
                'status' => VerificationResult::RESULT_INVALID_SIGNATURE,
            ];
        }

        return [
            'issuer' => $fileData['data']['issuer']['name'],
            'status' => VerificationResult::RESULT_VERIFIED,
        ];
    }

    /**
     * Validates the recipient in the file data.
     * 
     * @param array $fileData The file data to be validated.
     * @return bool True if the recipient is valid, false otherwise.
     */
    private function validateRecipient(array $fileData): bool
    {
        return isset($fileData['data']['recipient'])
            && isset($fileData['data']['recipient']['name'])
            && isset($fileData['data']['recipient']['email']);
    }

    /**
     * Validates the issuer in the file data.
     * 
     * @param array $fileData The file data to be validated.
     * @return bool True if the issuer is valid, false otherwise.
     */
    private function validateIssuer(array $fileData): bool
    {
        if (!isset($fileData['data']['issuer'])) {
            return false;
        }

        $requiredFields = ['name', 'identityProof'];
        foreach ($requiredFields as $field) {
            if (!isset($fileData['data']['issuer'][$field])) {
                return false;
            }
        }

        $identityProof = $fileData['data']['issuer']['identityProof'];
        $requiredIdentityFields = ['type', 'key', 'location'];
        foreach ($requiredIdentityFields as $field) {
            if (!isset($identityProof[$field])) {
                return false;
            }
        }

        //Perform DNS lookup for issuer validation
        try {
            $dnsLookupResult = $this->performDnsLookup($identityProof['location'], $identityProof['key']);
            return !empty($dnsLookupResult);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Performs a DNS lookup for the issuer's identity proof.
     * 
     * @param string $domain The domain to perform the DNS lookup on.
     * @param string $key The key to search for in the DNS records.
     * @return mixed The DNS lookup result.
     * @throws \Exception if the DNS lookup fails or returns an empty result.
     */
    private function performDnsLookup(string $domain, string $key)
    {
        $response = Http::get(config('app.dns_lookup_endpoint'), [
            'name' => $domain,
            'type' => 'TXT',
        ]);

        if ($response->ok()) {
            $result = $response->json();

            // Extract the TXT record values
            $dnsLookupResult = [];
            if (isset($result['Answer'])) {
                foreach ($result['Answer'] as $answer) {
                    if (isset($answer['data'])) {
                        // Extract the TXT record value without surrounding quotes
                        $txtRecord = trim($answer['data'], '"');
                        // Check if the key is present in the TXT record
                        if (strpos($txtRecord, $key) !== false) {
                            $dnsLookupResult[] = $txtRecord;
                        }
                    }
                }
            }

            return $dnsLookupResult;
        }

        // Throw an exception if DNS lookup fails or returns empty result
        throw new \Exception('DNS lookup failed');
    }

    /**
     * Validates the signature in the file data.
     * 
     * @param array $fileData The file data to be validated.
     * @return bool True if the signature is valid, false otherwise.
     */
    private function validateSignature(array $fileData): bool
    {
        $targetHash = $this->computeTargetHash($fileData);
        return $targetHash === $fileData['signature']['targetHash'];
    }

    /**
     * Computes the target hash for signature validation.
     * 
     * @param array $fileData The file data to compute the target hash for.
     * @return string The computed target hash.
     */
    private function computeTargetHash(array $fileData): string
    {
        $properties = [
            'id' => $fileData['data']['id'],
            'name' => $fileData['data']['name'],
            'recipient.name' => $fileData['data']['recipient']['name'],
            'recipient.email' => $fileData['data']['recipient']['email'],
            'issuer.name' => $fileData['data']['issuer']['name'],
            'issuer.identityProof.type' => $fileData['data']['issuer']['identityProof']['type'],
            'issuer.identityProof.key' => $fileData['data']['issuer']['identityProof']['key'],
            'issuer.identityProof.location' => $fileData['data']['issuer']['identityProof']['location'],
            'issued' => $fileData['data']['issued'],
        ];

        $hashes = [];
        foreach ($properties as $key => $value) {
            $property = '{"' . $key . '":"' . $value . '"}';
            $hash = hash('sha256', $property);
            $hashes[] = $hash;
        }

        sort($hashes, SORT_STRING);

        return hash('sha256', json_encode($hashes));
    }

    /**
     * Saves the verification result to the database.
     * 
     * @param array $verificationResult The verification result to be saved.
     * @return void
     */
    private function saveVerificationResult($verificationResult): void
    {
        VerificationResult::create([
            'user_id' => auth()->id(),
            'file_type' => VerificationResult::FILE_TYPE_JSON,
            'verification_result' => $verificationResult['status'],
            'timestamp' => now(),
        ]);
    }

    /**
     * Sets the verification result model.
     * 
     * @param mixed $verificationResultModel The verification result model to be set.
     * @return void
     */
    public function setVerificationResultModel($verificationResultModel): void
    {
        $this->verificationResultModel = $verificationResultModel;
    }
}
