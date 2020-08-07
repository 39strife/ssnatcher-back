<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    private function JSONSeed()
    {
        $data = File::get("database/data/db.json");
        $data = json_decode($data, true);
        foreach ($data as $obj) {
            DB::table($obj['table'])->delete();
            $model = $obj['model'];
            // error_log($obj['model']);
            foreach ($obj['rows'] as $row) {
                // error_log(json_encode($row));
                $data = new $model($row);
                $data->save();
            }
        }
    }
    public function run()
    {
        $this->JSONSeed();
    }
}
