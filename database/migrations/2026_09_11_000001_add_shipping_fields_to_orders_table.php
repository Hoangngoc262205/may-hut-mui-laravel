<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone');
            $table->string('province_id')->nullable()->after('address');
            $table->string('province_name')->nullable()->after('province_id');
            $table->string('district_id')->nullable()->after('province_name');
            $table->string('district_name')->nullable()->after('district_id');
            $table->string('ward_code')->nullable()->after('district_name');
            $table->string('ward_name')->nullable()->after('ward_code');
            $table->decimal('shipping_fee', 12, 0)->default(0)->after('subtotal');
            $table->string('ghn_order_code')->nullable()->after('status');
            $table->decimal('ghn_fee', 12, 0)->nullable()->after('ghn_order_code');
            $table->string('ghn_status')->nullable()->after('ghn_fee');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'province_id',
                'province_name',
                'district_id',
                'district_name',
                'ward_code',
                'ward_name',
                'shipping_fee',
                'ghn_order_code',
                'ghn_fee',
                'ghn_status',
            ]);
        });
    }
};
