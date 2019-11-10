<?php

use App\Models\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 10);
            $table->string('alias', 50);
            $table->index('name');
            $table->index('alias');
            $table->timestamps();
        });
        $currencyMap = [
            'JPY' => '日幣',
            'USD' => '美金',
            'CNY' => '人民幣',
            'KRW' => '韓幣',
            'GBP' => '英鎊',
            'EUR' => '歐元',
            'THB' => '泰銖',
            'HKD' => '港幣',
            'AUD' => '澳幣',
        ];
        foreach ($currencyMap as $alias => $name) {
            Currency::create([
                'name' => $name,
                'alias' => $alias,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
