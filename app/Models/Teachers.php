<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Teachers extends Authenticatable
{
	use HasFactory, Notifiable, HasApiTokens;

	protected $primaryKey = 'teacherID'; // Explicitly set the primary key

	protected $fillable = [
		'firstname',
		'lastname',
		'email',
		'password'
	];

	protected $hidden = [
		'password'
	];

	protected function casts(): array
	{
		return [
			'password' => 'hashed'
		];
	}

	public $timestamps = false;
}
