<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUploadedByToUploadsTable extends Migration
{
    public function up()
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->unsignedBigInteger('uploaded_by')->nullable()->after('id'); // Make the field nullable
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn('uploaded_by');
        });
    }
}
