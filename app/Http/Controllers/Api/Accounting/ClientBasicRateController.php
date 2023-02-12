<?php

namespace App\Http\Controllers\Api\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\ClientBasicRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ClientBasicRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        Builder::macro('whereLike', function($columns, $search) {
            $this->where(function($query) use ($columns, $search) {
                foreach(Arr::wrap($columns) as $column) {
                    $query->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        
            return $this;
        });

        $basic_rates = ClientBasicRate::
            when($request->search, function ($query) use ($request) {
                $query->whereLike(['tblm_client_basic_rate.id','tblm_client.client_name', 'tblm_salary_type.desc'], $request->search);
            })
            ->select(
            'tblm_client_basic_rate.*', 'tblm_client.client_name as client_name', 
            'tblm_salary_type.id as s_type_id', 'tblm_salary_type.desc as s_type_desc')
            ->join('tblm_salary_type', 'tblm_client_basic_rate.salary_type', '=', 'tblm_salary_type.id')
            ->join('tblm_client', 'tblm_client_basic_rate.link_client_id', '=', 'tblm_client.id')
            ->orderBy('id', 'DESC')->paginate($request->limit ? $request->limit : ClientBasicRate::count());
        // $basic_rates = ClientBasicRate::select(
        //     'tblm_client_basic_rate.*', 'tblm_client.client_name as client_name', 
        //     'tblm_salary_type.id as s_type_id', 'tblm_salary_type.desc as s_type_desc')
        //     ->leftjoin('tblm_salary_type', 'tblm_client_basic_rate.salary_type', '=', 'tblm_salary_type.id')
        //     ->leftjoin('tblm_client', 'tblm_client_basic_rate.link_client_id', '=', 'tblm_client.id')
        //     ->orderBy('id', 'DESC')->paginate($request->limit ? $request->limit : ClientBasicRate::count());

        return response()->json([
            'success' => true,
            'data' => $basic_rates,
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Set validation
		$validator = Validator::make($request->all(), [
            'link_client_id' => 'required:mysql2.tblm_client_basic_rate',
            'basic_monthly_rate' => 'required:mysql2.tblm_client_basic_rate',
            'effective_date_from' => 'required:mysql2.tblm_client_basic_rate',
            'effective_date_to' => 'required:mysql2.tblm_client_basic_rate',
        ],
        [
            'link_client_id.required' => 'The Client is requried.',
            'basic_monthly_rate.required' => 'The Monthly Rate is requried.',
            'effective_date_from.required' => 'The Effective Date From is requried.',
            'effective_date_to.required' => 'The Effective Date To is requried.',
        ]);


        //If validation fails
		if ($validator->fails()) {
			return response()->json([
                'errors' => $validator->errors()
            ], 422);
		}

        $basic_rate = ClientBasicRate::create(
            [
                'link_client_id' => $request->link_client_id,
                'salary_type' => $request->salary_type,
                'basic_monthly_rate' => $request->basic_monthly_rate,
                'basic_weekly_rate' => $request->basic_weekly_rate,
                'basic_daily_rate' => $request->basic_daily_rate,
                'basic_hourly_rate' => $request->basic_hourly_rate,
                'effective_date_from' => $request->effective_date_from,
                'effective_date_to' => $request->effective_date_to,
                'is_active' => $request->is_active,
                'createdby' => auth()->user()->id,
                'datecreated' => Carbon::now()
            ]
        );
        
		$basic_rate = ClientBasicRate::orderBy('id', 'desc')->paginate($request->limit);

        return response()->json([
            'success' => true,
            'data' => $basic_rate,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Set validation
		$validator = Validator::make($request->all(), [
            'client_name' => 'required|max:50|:mysql2.tblm_client,client_name',
        ],
        [
            'client_name.required' => 'The Client Name is requried.',
            'client_name.unique' => 'The Client Name already exist.'
        ]);

        //If validation fails
		if ($validator->fails()) {
			return response()->json([
                'errors' => $validator->errors()
            ], 422);
		}

        ClientBasicRate::where('id', $request->id)->update(
            [
                'link_client_id' => $request->link_client_id,
                'salary_type' => $request->salary_type,
                'basic_monthly_rate' => $request->basic_monthly_rate,
                'basic_weekly_rate' => $request->basic_weekly_rate,
                'basic_daily_rate' => $request->basic_daily_rate,
                'basic_hourly_rate' => $request->basic_hourly_rate,
                'effective_date_from' => $request->effective_date_from,
                'effective_date_to' => $request->effective_date_to,
                'is_active' => $request->is_active,
                'modifiedby' => auth()->user()->id,
                'datemodified' => Carbon::now()
            ]
        );

        return response()->json([
            'success' => true,
            'data' => ClientBasicRate::find($request->id),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */    
    public function delete(Request $request)
    {
        ClientBasicRate::wherein('id', $request->id)->delete();

		$basic_rate = ClientBasicRate::orderBy('id', 'desc')->paginate($request->limit);

        return response()->json([
            'success' => true,
            'data' => $basic_rate,
        ], 200);
    }
}
