<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimesheetDetail extends Model
{
    use HasFactory;

	//Disable timetamps default fields of update_at and create_at
	public $timestamps = false;

    protected $connection = 'mysql3';

    //Rename table user schema
	protected $table = 'tblt_timesheet_dtl';

    public function works()
    {
        return $this->hasMany(TimesheetDetail::class, 'date_worked', 'date_worked');
    }

    public function lunch()
    {
        return $this->hasOne(TimesheetDetail::class, 'date_worked', 'date_worked');
    }
}
