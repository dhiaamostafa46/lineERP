<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 use Carbon\Carbon;
use Modules\HR\App\Models\HrAttendance;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


// $employeeId = 28;
// $placeId = 1;
// $sd = "2026-05-01 00:30:00";
// $ed = "2026-05-31 23:30:00";
// $startDate = Carbon::parse($sd);//Carbon::now()->subMonths(3)->startOfDay();
// $endDate   = Carbon::parse($ed);;//Carbon::now()->endOfDay();

// $current = $startDate->copy();

// while ($current->lte($endDate)) {

//     // العمل من الأحد إلى الخميس فقط
//     if (!in_array($current->dayOfWeek, [
//         Carbon::SUNDAY,
//         Carbon::MONDAY,
//         Carbon::TUESDAY,
//         Carbon::WEDNESDAY,
//         Carbon::THURSDAY,
//     ])) {
//         $current->addDay();
//         continue;
//     }

// $absenceStart = Carbon::createFromFormat('d-m-Y', '24-05-2026')->startOfDay();
//     $absenceEnd   = Carbon::createFromFormat('d-m-Y', '30-05-2026')->endOfDay();

//      if ($current->between($absenceStart, $absenceEnd)) {
//           $current->addDay();
//           continue;
//          }

//     // 3% غياب
//     if (rand(1, 100) <= 3) {
//         $current->addDay();
//         continue;
//     }

//     /*
//      * سيناريو اليوم
//      * 1-10  => تأخير
//      * 11-20 => حضور مبكر
//      * 21-25 => أوفر تايم
//      * 26-30 => خروج مبكر
//      * الباقي => طبيعي
//      */
//     $scenario = rand(1, 100);

//     $morningArrivalOffset = rand(-5, 10);
//     $morningLeaveOffset   = rand(-10, 10);
//     $eveningArrivalOffset = rand(-5, 10);
//     $eveningLeaveOffset   = rand(-10, 15);

//     if ($scenario <= 10) {

//         // تأخير
//         $morningArrivalOffset = rand(20, 45);
//         $eveningArrivalOffset = rand(5, 20);

//     } elseif ($scenario <= 20) {

//         // حضور مبكر
//         $morningArrivalOffset = rand(-20, -5);
//         $eveningArrivalOffset = rand(-10, 0);

//     } elseif ($scenario <= 25) {

//         // أوفر تايم
//         $eveningLeaveOffset = rand(30, 90);

//     } elseif ($scenario <= 30) {

//         // خروج مبكر
//         $eveningLeaveOffset = -rand(20, 60);
//     }

//     // الفترة الأولى
//     $checkInMorning = $current->copy()
//         ->setTime(9, 0)
//         ->addMinutes($morningArrivalOffset);

//     $checkOutMorning = $current->copy()
//         ->setTime(13, 0)
//         ->addMinutes($morningLeaveOffset);

//     // الفترة الثانية
//     $checkInEvening = $current->copy()
//         ->setTime(14, 0)
//         ->addMinutes($eveningArrivalOffset);

//     $checkOutEvening = $current->copy()
//         ->setTime(18, 0)
//         ->addMinutes($eveningLeaveOffset);

//     $records = [

//         // دخول صباحي
//         [
//             'time'       => $checkInMorning,
//             'type'       => 1,
//             'shift_from' => '09:00:00',
//             'shift_to'   => '13:00:00',
//         ],

//         // خروج صباحي
//         [
//             'time'       => $checkOutMorning,
//             'type'       => 2,
//             'shift_from' => '09:00:00',
//             'shift_to'   => '13:00:00',
//         ],

//         // دخول مسائي
//         [
//             'time'       => $checkInEvening,
//             'type'       => 1,
//             'shift_from' => '14:00:00',
//             'shift_to'   => '18:00:00',
//         ],

//         // خروج مسائي
//         [
//             'time'       => $checkOutEvening,
//             'type'       => 2,
//             'shift_from' => '14:00:00',
//             'shift_to'   => '18:00:00',
//         ],
//     ];

//     foreach ($records as $record) {

//         HrAttendance::create([

//             'employee_id' => $employeeId,
//             'places_id'   => $placeId,

//             'day'         => $current->dayOfWeek,
//             'date'        => $record['time']->toDateString(),
//             'check_time'  => $record['time']->format('H:i:s'),

//             'shift_from'  => $record['shift_from'],
//             'shift_to'    => $record['shift_to'],

//             'name'        => 'Main Office',
//             'lat'         => '24.7136',
//             'lon'         => '46.6753',
//             'address'     => 'Riyadh',

//             'delay'         => 0,
//             'early_leave'   => 0,
//             'overtime'      => 0,
//             'early_arrival' => 0,

//             'Active'      => 1,
//             'kind'        => 1,
//             'type'        => $record['type'],
//             'status'      => 2,
//             'distance'    => rand(1, 20),

//             'created_at'  => $record['time'],
//             'updated_at'  => $record['time'],
//         ]);
//     }

//     $current->addDay();
// }
    }
}
