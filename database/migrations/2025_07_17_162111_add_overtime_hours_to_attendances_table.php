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
                // Tambahkan kolom 'overtime_hours' sebagai float atau decimal, dengan nilai default 0
                $table->float('overtime_hours')->default(0)->after('status'); // Setelah kolom 'status'
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
                $table->dropColumn('overtime_hours');
            });
        }
    };
    