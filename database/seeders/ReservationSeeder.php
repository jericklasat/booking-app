<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      $reservations = [];
      $date = now();
      $userId = 1;

      for($i = 0; $i < 150; $i++) {
        $date->add($i + 1, 'day');

        if (count($reservations) === 500) {
          break;
        }

        if ($date->isWeekend()) {
          continue;
        }

        for($roomId = 1; $roomId < 6; $roomId++) {
          $selectedHour = rand(7, 15);
          $selectedDuration = rand(15, 60);

          $reservations[] = [
            'title' => fake()->text,
            'room_id' => $roomId,
            'user_id' => $userId,
            'start_date_time' => Carbon::create($date->year, $date->month, $date->day, $selectedHour, 0, 0),
            'end_date_time' => Carbon::create($date->year, $date->month, $date->day, $selectedHour, $selectedDuration, 0),
            'created_at' => now(),
          ];
        }

        $userId++;
      }

      DB::table('reservations')->insert($reservations);
    }
}
