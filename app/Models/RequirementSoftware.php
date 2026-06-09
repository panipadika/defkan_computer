<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementSoftware extends Model
{
    use HasFactory;

    protected $table = 'requirement_software';
    protected $guarded = ['id']; // id, software_id, min_ram, min_vga, min_storage, etc.
    public $timestamps = false;
}
