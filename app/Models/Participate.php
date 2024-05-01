<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participate extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function participantSubject(){
        return $this->hasMany(ParticipantSubject::class,'participant_id','id');
    }
    public function participantUser(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function participantSchool(){
        return $this->belongsTo(School::class,'school_id','id');
    }

    public function participantOlympiad(){
        return $this->belongsTo(Olympiad::class,'olympiad_id','id');
    }
}
