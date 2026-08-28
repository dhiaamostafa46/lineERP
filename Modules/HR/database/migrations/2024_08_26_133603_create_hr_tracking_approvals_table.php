
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('hr_tracking_approvals')) {
            Schema::create('hr_tracking_approvals', function (Blueprint $table) {
                $table->id();
                $table->morphs('trackable');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->unsignedInteger('sort');
                $table->boolean('is_current')->default(0);
                $table->unsignedTinyInteger('status')->default(1)->comment('1 = Pending, 2 = Approved, 3 = Rejected');
                $table->text('note')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hr_tracking_approvals');
    }
};
