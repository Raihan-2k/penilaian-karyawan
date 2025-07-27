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
            Schema::table('attendances', function (Blueprint $table) {
                // Tambahkan kolom 'status' sebagai string, dengan nilai default 'present'
                // Anda bisa menyesuaikan default atau menjadikannya nullable jika diperlukan.
                $table->string('status')->default('present')->after('check_out_time');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    };
    