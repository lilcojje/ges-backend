<?php

namespace App\Models\Admin;

use App\Models\Users\OnboardProfileBasicInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRemoteContractor extends Model
{
    use HasFactory;

    //Disable timetamps default fields of update_at and create_at
	public $timestamps = false;

    //Rename table user schema
	protected $table = 'tblm_client_sub_contractor';

    protected $connection = 'mysql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reg_link_preregid',
        'actreg_contractor_id',
        'date_contracted',
        'status',
        'createdby',
        'datecreated',
        'modifiedby',
        'datemodified'
    ];

    public function clients()
    {
        return $this->hasMany(ClientRemoteContractorPersonnel::class, 'link_subcon_id');
    }
}
