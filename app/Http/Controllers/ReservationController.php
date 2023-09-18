<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function getRoomReservations(string $roomId)
    {
        return response(
            [
                'reservations' => DB::table('reservations')
                    ->join('users', 'reservations.user_id', '=', 'users.id')
                    ->where('reservations.room_id', '=', intval($roomId))
                    ->select('reservations.*', 'users.name')
                    ->get()
            ]
        );
    }

    public function createReservation(Request $request)
    {
        $this->validate($request, [
            'start' => 'required',
            'end' => 'required',
            'title' => 'required',
            'room_id' => 'required',
            'user_id' => 'required',
        ]);


        $start = Carbon::createFromFormat('Y-m-d H:i:s', $request->start);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $request->end);

        if ($start->diffInSeconds($end) < 900) {
            return response(['message' => 'Reservation should not be less than 15 minutes.'], 200);
        }

        if ($start->diffInSeconds($end) > 3600) {
            return response(['message' => 'Reservation should not exceed to 1 hour.'], 200);
        }

        if ($this->isEventConflict($start, $end, $request->room_id)) {
            return response(['message' => 'Reservation is conflict.'], 200);
        }

        $reservation = new Reservation();
        $reservation->room_id = $request->room_id;
        $reservation->user_id = $request->user_id;
        $reservation->title = $request->title;
        $reservation->start_date_time = $request->start;
        $reservation->end_date_time = $request->end;
        $reservation->save();

        return response(['message' => 'Reservation created.'], 200);
    }

    public function updateReservation(Request $request)
    {
        $this->validate($request, [
            'start' => 'required',
            'end' => 'required',
            'title' => 'required',
            'event_id' => 'required',
        ]);


        $start = Carbon::createFromFormat('Y-m-d H:i:s', $request->start);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $request->end);

        if ($start->diffInSeconds($end) < 900) {
            return response(['message' => 'Reservation should not be less than 15 minutes.'], 200);
        }

        if ($start->diffInSeconds($end) > 3600) {
            return response(['message' => 'Reservation should not exceed to 1 hour.'], 200);
        }

        if ($this->isEventConflictForUpdate($start, $end, $request->event_id)) {
            return response(['message' => 'Reservation is conflict.'], 200);
        }

        Reservation::where('id', (int) $request->event_id)
            ->update([
                'title' => $request->title,
                'start_date_time' => $request->start,
                'end_date_time' => $request->end,
            ]);

        return response(['message' => 'Reservation updated.'], 200);
    }

    public function deleteReservation(Request $request)
    {
        try {
            $reservation = Reservation::find((int) $request->deletedId);
            $reservation->delete();

            return response(['message' => 'Reservation deleted successfully.'], 200);
        } catch (Exception $e) {
            return response(['message' => 'There is an error in deleting reservation.'], 500);
        }
    }

    public function getUserReservations(int $userId)
    {
        try {
            $reservations = DB::table('reservations')
                ->join('rooms', 'reservations.room_id', '=', 'rooms.id')
                ->where('reservations.user_id', '=', $userId)
                ->select('reservations.*', 'rooms.name as room_name')
                ->get();

            return response(['reservations' => $reservations], 200);
        } catch (Exception $e) {
            return response(['message' => 'There is an error in fetching reservations.'], 500);
        }
    }

    private function isEventConflict(Carbon $from, Carbon $to, int $roomId): bool
    {
        $reservations = DB::table('reservations')
            ->where('room_id', '=', $roomId)
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('start_date_time', [$from->toDateTime(), $to->toDateTime()])
                    ->orWhereBetween('end_date_time', [$from->toDateTime(), $to->toDateTime()]);
            })
            ->get();

        return count($reservations) > 0;
    }

    private function isEventConflictForUpdate(Carbon $from, Carbon $to, int $eventId): bool
    {
        $reservations = DB::table('reservations')
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('start_date_time', [$from->toDateTime(), $to->toDateTime()])
                    ->orWhereBetween('end_date_time', [$from->toDateTime(), $to->toDateTime()]);
            })
            ->get();

        if (count($reservations) > 1) {
            return true;
        }

        if (count($reservations) === 1 && $reservations[0]->id === $eventId) {
            return false;
        }

        return count($reservations) > 0;
    }
}
