<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantSubject extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function participant(){
        return $this->belongsTo(Participate::class,'participant_id','id');
    }
}
