<?php

namespace Database\Seeders;

use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PublicHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $holidays = [
            // Public Holidays of Nepal 2083 BS (approximate dates)
            ['title' => 'New Year', 'date' => '2083-01-01', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Buddha Jayanti', 'date' => '2083-01-18', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Eid', 'date' => '2083-02-14', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Ganatantra Diwas', 'date' => '2083-02-15', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Janai Purnima', 'date' => '2083-05-12', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Krishna Janmasthami', 'date' => '2083-05-19', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Teej', 'date' => '2083-05-29', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Indrajatra', 'date' => '2083-06-09', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Maha Asthami', 'date' => '2083-07-01', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Dashai Holiday', 'date' => '2083-07-02', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Maha Nabami', 'date' => '2083-07-03', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Vijaya Dashami', 'date' => '2083-07-03', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Dashai Holiday', 'date' => '2083-07-03', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Maha Shivaratri', 'date' => '2083-11-20', 'is_holiday' => true, 'created_by' => 1],
            ['title' => 'Holi', 'date' => '2083-12-07', 'is_holiday' => true, 'created_by' => 1],
        ];

        // Insert public holidays
        foreach ($holidays as $holiday) {
            DB::table('calendars')->insert($holiday);
        }
    }
}
