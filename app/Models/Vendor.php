<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_vendor';
    protected $table = 'vendors';

    protected $fillable = [
        'nama_vendor',
    ];

    public function externalEmployees()
    {
        return $this->hasMany(ExternalEmployee::class, 'id_vendor', 'id_vendor');
    }
}