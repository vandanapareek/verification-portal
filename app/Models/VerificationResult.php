<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationResult extends Model
{

    const FILE_TYPE_JSON = 'json';

    const RESULT_VERIFIED = 'verified';
    const RESULT_INVALID_RECIPIENT = 'invalid_recipient';
    const RESULT_INVALID_ISSUER = 'invalid_issuer';
    const RESULT_INVALID_SIGNATURE = 'invalid_signature';

    protected $fillable = [
        'user_id',
        'file_type',
        'verification_result',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

