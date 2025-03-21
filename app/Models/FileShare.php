<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileShare extends Model
{
    use HasFactory;

    protected $fillable = ['file_id', 'shared_with_user_email'];

    public function file()
    {
        return $this->belongsTo(Upload::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'shared_with_user_email', 'email'); 
    }
}