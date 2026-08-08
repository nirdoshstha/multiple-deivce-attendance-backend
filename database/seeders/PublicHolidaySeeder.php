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
        //     $holidays = [
        //         // Major public holidays in 2083 BS (approximate Nepali dates)
        //         ['title' => 'New Year', 'date' => '2083-01-01', 'slug' => 'new-year'], // Nepali New Year
        //         ['title' => 'Maha Shivaratri', 'date' => '2083-11-29', 'slug' => 'maha-shivaratri'],
        //         ['title' => 'Holi', 'date' => '2083-12-15', 'slug' => 'holi'],
        //         ['title' => 'Buddha Jayanti', 'date' => '2083-02-08', 'slug' => 'buddha-jayanti'],
        //         ['title' => 'Indra Jatra', 'date' => '2083-06-22', 'slug' => 'indra-jatra'],
        //         ['title' => 'Tihar', 'date' => '2083-07-15', 'slug' => 'tihar'],
        //         ['title' => 'Christmas', 'date' => '2083-09-10', 'slug' => 'christmas'], // For example

        //         // Saturdays for the whole year 2083 BS (Saturday is week day 7)
        //         ['title' => 'Saturday', 'date' => '2083-01-03', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-01-10', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-01-17', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-01-24', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-01-31', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-02-07', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-02-14', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-02-21', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-02-28', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-03-06', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-03-13', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-03-20', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-03-27', 'slug' => 'saturday'],
        //         ['title' => 'Saturday', 'date' => '2083-04-02', 'slug' => 'saturday'],
        //         // Continue Saturdays for entire 2083 year as needed
        //     ];

        //     DB::table('holidays')->insert($holidays);
        //

        $holidays = [
            // Public Holidays of Nepal 2083 BS (approximate dates)
            ['title' => 'New Year', 'date' => '2083-01-01', 'slug' => 'new-year', 'is_holiday' => true],
            ['title' => 'Buddha Jayanti', 'date' => '2083-01-18', 'slug' => 'buddha-jayanti', 'is_holiday' => true],
            ['title' => 'Eid', 'date' => '2083-02-14', 'slug' => 'eid', 'is_holiday' => true],
            ['title' => 'Ganatantra Diwas', 'date' => '2083-02-15', 'slug' => 'ganatantra-diwas', 'is_holiday' => true],
            ['title' => 'Janai Purnima', 'date' => '2083-05-12', 'slug' => 'janai-purnima', 'is_holiday' => true],
            ['title' => 'Krishna Janmasthami', 'date' => '2083-05-19', 'slug' => 'krishna-janmasthami', 'is_holiday' => true],
            ['title' => 'Teej', 'date' => '2083-05-29', 'slug' => 'teej', 'is_holiday' => true],
            ['title' => 'Indrajatra', 'date' => '2083-06-09', 'slug' => 'indra-jatra', 'is_holiday' => true],
            ['title' => 'Maha Asthami', 'date' => '2083-07-01', 'slug' => 'maha-asthami', 'is_holiday' => true],
            ['title' => 'Dashai Holiday', 'date' => '2083-07-02', 'slug' => 'dashai-holiday', 'is_holiday' => true],
            ['title' => 'Maha Nabami', 'date' => '2083-07-03', 'slug' => 'maha-nabami', 'is_holiday' => true],
            ['title' => 'Vijaya Dashami', 'date' => '2083-07-03', 'slug' => 'vijaya-dashami', 'is_holiday' => true],
            ['title' => 'Dashai Holiday', 'date' => '2083-07-03', 'slug' => 'dashai-holiday', 'is_holiday' => true],
            ['title' => 'Maha Shivaratri', 'date' => '2083-11-20', 'slug' => 'maha-shivaratri', 'is_holiday' => true],
            ['title' => 'Holi', 'date' => '2083-12-07', 'slug' => 'holi', 'is_holiday' => true],
        ];

        // Insert public holidays
        foreach ($holidays as $holiday) {
            DB::table('calendars')->insert($holiday);
        }

        // Insert Saturdays of 2083 BS
        // Saturdays occur weekly; you can generate them programmatically
        $start_date = '2083-01-01'; // Nepali date format YYYY-MM-DD
        $end_date = '2083-12-30';   // End of the year

        $start = new DateTime($this->convertNepaliToEnglishDate($start_date));
        $end = new DateTime($this->convertNepaliToEnglishDate($end_date));

        // Find the first Saturday
        while ($start->format('N') != 6) { // 6 is Saturday in ISO-8601 numeric representation
            $start->modify('+1 day');
        }

        while ($start <= $end) {
            $nep_date = $this->convertEnglishToNepaliDate($start->format('Y-m-d'));
            DB::table('calendars')->insert([
                'title' => 'Saturday',
                'date' => $nep_date,
                'slug' => 'saturday',
            ]);
            $start->modify('+7 days'); // Next Saturday
        }
    }

    // You would need to implement conversion functions or use a library for Nepali date conversion.
    private function convertNepaliToEnglishDate($nep_date)
    {
        // Dummy function - implement actual conversion or use a package
        return '2026-04-14'; // example fixed date for 2083-01-01
    }

    private function convertEnglishToNepaliDate($eng_date)
    {
        // Dummy function - implement actual conversion or use a package
        return '2083-01-01'; // example fixed date for 2026-04-14
    }
}
