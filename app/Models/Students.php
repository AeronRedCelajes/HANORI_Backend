<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Students extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

	protected $primaryKey = 'studentID'; // Explicitly set the primary key

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
