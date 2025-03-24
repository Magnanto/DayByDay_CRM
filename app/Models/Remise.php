<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remise extends Model
{
    protected $table = 'remise';
    protected $fillable = ['discount'];

    public function updateRemise($id,$discount){
        $remise=self::find($id);
        $remise->discount=$discount;
        $remise->updated_at=date('Y-m-d H:i:s');
        $remise->save();
    }
}