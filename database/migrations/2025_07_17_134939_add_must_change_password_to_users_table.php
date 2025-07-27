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
            Schema::table('users', function (Blueprint $table) {
                // Tambahkan kolom 'must_change_password' sebagai boolean, default true
                // Ini akan memaksa user untuk ganti password saat login pertama kali
                $table->boolean('must_change_password')->default(true)->after('password');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('users', function (Blueprint $table) {
                // Hapus kolom 'must_change_password' jika migrasi di-rollback
                $table->dropColumn('must_change_password');
            });
        }
    };
    