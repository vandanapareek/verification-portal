<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\VerificationResult;

class CreateVerificationResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verification_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('file_type', [
                VerificationResult::FILE_TYPE_JSON,
            ]);
            $table->enum('verification_result', [
                VerificationResult::RESULT_VERIFIED,
                VerificationResult::RESULT_INVALID_RECIPIENT,
                VerificationResult::RESULT_INVALID_ISSUER,
                VerificationResult::RESULT_INVALID_SIGNATURE,
            ]);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verification_results');
    }
}
