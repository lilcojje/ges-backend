<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Client\TimesheetDetail;
use App\Models\Client\TimesheetHeader;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRemoteContractorTimesheet(Request $request)
    {
        //Set validation
		$validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'remote_contractor_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);

        //If validation fails
		if ($validator->fails()) {
			return response()->json([
                'errors' => $validator->errors()
            ], 422);
		}

        $timesheet_header = TimesheetHeader::
        where('link_client_id', $request->client_id)
        ->where('link_subcon_id', $request->remote_contractor_id)
        ->first();

        if(!$timesheet_header) {
            return response()->json([
				'success' => false,
				'message' => 'No data found.',
			], 200);
        }

        $total_work_hours = TimesheetDetail::
        select('id', 'link_tms_hdr_id', 'date_worked', 'work_total_hours')
        ->where('link_tms_hdr_id', $timesheet_header->id)
        ->whereDate('date_worked', '>=', $request->start_date)
        ->whereDate('date_worked', '<=', $request->end_date)
        ->where('dtl_type', 'work')
        ->get()
        ->sum('work_total_hours');

        $total_lunch_hours = TimesheetDetail::
        select('id', 'link_tms_hdr_id', 'date_worked', 'work_total_hours')
        ->where('link_tms_hdr_id', $timesheet_header->id)
        ->whereDate('date_worked', '>=', $request->start_date)
        ->whereDate('date_worked', '<=', $request->end_date)
        ->where('dtl_type', 'lunch')
        ->get()
        ->sum('work_total_hours');

        $timesheet_details = TimesheetDetail::
        select('id', 'link_tms_hdr_id', 'date_worked', 'work_time_in', 'dtl_type')
        ->withSum(['works' => function($query) use ($timesheet_header) {
            $query->groupBy('date_worked')
                    ->where('link_tms_hdr_id', $timesheet_header->id)
                    ->where('dtl_type', 'work');
        }], 'work_total_hours')
        ->with(['works' => function ($query) use ($timesheet_header){
            $query->where('link_tms_hdr_id', $timesheet_header->id)
            ->where('dtl_type', 'work');
        }])
        ->with(['lunch' => function ($query) use ($timesheet_header){
            $query->where('link_tms_hdr_id', $timesheet_header->id)
            ->where('dtl_type', 'lunch');
        }])
        ->where('link_tms_hdr_id', $timesheet_header->id)
        ->whereDate('date_worked', '>=', $request->start_date)
        ->whereDate('date_worked', '<=', $request->end_date)
        ->where('dtl_type', 'work')
        ->groupBy('date_worked')
        ->orderBy('id', 'desc')
        ->paginate($request->limit ? $request->limit : TimesheetDetail::count());

        return response()->json([
            'success' => true,
            'total_actual_work_hours' => number_format($total_work_hours - $total_lunch_hours, 2),
            'total_adjusted_hours' => number_format(0, 2),
            'total_regular_work_hours' => number_format(0, 2),
            'data' => $timesheet_details,
        ], 200);
    }
}
