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
            Schema::table('sales_validations', function (Blueprint $table) {
                // Tambahkan kolom 'validated_at' sebagai timestamp, bisa nullable jika tidak selalu ada
                $table->timestamp('validated_at')->nullable()->after('status');
                // Jika Anda juga tidak memiliki 'validation_notes' dan ingin menambahkannya:
                // $table->text('validation_notes')->nullable()->after('validated_at');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('sales_validations', function (Blueprint $table) {
                $table->dropColumn('validated_at');
                // Jika Anda menambahkan 'validation_notes' di up(), hapus juga di sini
                // $table->dropColumn('validation_notes');
            });
        }
    };
    