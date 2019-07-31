<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Searchcase extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE FUNCTION toUpperCase(character varying) RETURNS character varying AS $$
DECLARE
  hdbk varchar[] := ARRAY[
	['а','А'],
	['б','Б'],
	['в','В'],
	['г','Г'],
	['д','Д'],
	['е','Е'],
	['ё','Ё'],
	['ж','Ж'],
	['з','З'],
	['и','И'],
	['й','Й'],
	['к','К'],
	['л','Л'],
	['м','М'],
	['н','Н'],
	['о','О'],
	['п','П'],
	['р','Р'],
	['с','С'],
	['т','Т'],
	['у','У'],
	['ф','Ф'],
	['х','Х'],
	['ц','Ц'],
	['ч','Ч'],
	['ш','Ш'],
	['щ','Щ'],
	['ъ','Ъ'],
	['ы','Ы'],
	['ь','Ь'],
	['э','Э'],
	['ю','Ю'],
	['я','Я']
  ];
  x varchar[];
  result character varying := $1;
BEGIN
  FOR x IN 1 .. array_upper(hdbk, 1)
  LOOP
    result := replace(result, hdbk[x][1], hdbk[x][2]);
  END LOOP;
  RETURN result;
END;
$$ LANGUAGE plpgsql;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP FUNCTION IF EXISTS toUpperCase (character varying);");
    }

}
