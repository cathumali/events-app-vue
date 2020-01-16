<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Calendar;
use Carbon\CarbonPeriod;

class CalendarController extends Controller
{
	public function events(){

		$events = Calendar::orderBy('event_date', 'desc')->get();
		
		if(count($events)) {

			$start_date = $events[count($events)-1];
			$end_date   = $events[0];
			
			$period = CarbonPeriod::create($start_date['event_date'], $end_date['event_date']);

			$day_array = [];
			foreach ($period as $date) {
				$date = $date->format('Y-m-d');
				$day_array[$date] = null;
			}

			
			$events = array_merge($day_array, $events->groupBy('event_date')->toArray());

			return response()->json($events);

		}else{
			return response()->json([]);
		}

	}
 	public function store(Request $request)
    {	
    	$events = [];

		foreach ($request->all() as $key => $event) {

	        $event = Calendar::updateOrCreate(
			['event_date' => $event['date'] ]
			,[
	            'event_name' => $event['name'],
	            'event_date' => $event['date']
	        ]);
	       

	        $events[] = $event;
    	}

        return response()->json($events);
    }
}
