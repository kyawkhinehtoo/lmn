<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_templates';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'title',
        'body',
        'cc_email',
        'type',
        'default',
        'created_at',
        'updated_at'
     ];
}