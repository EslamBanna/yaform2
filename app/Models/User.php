<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'num_of_employees',
        'img_src',
        'url',
        'country',
        'gender',
        'business_category',
        'company_name',
        'year_dob',
        'month_dob',
        'day_dob',
        'reset_password_code',
        'type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getImgSrcAttribute($value)
    {
        $actual_link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        return ($value == null ? '' : $actual_link . 'images/users/' . $value);
    }

    public function getNameAttribute($value)
    {
        return $value ?? "";
    }
    public function getEmailAttribute($value)
    {
        return $value ?? "";
    }
    public function getPhoneAttribute($value)
    {
        return $value ?? "";
    }
    public function getGenderAttribute($value)
    {
        return $value ?? "";
    }
    public function getNumOfEmployeesAttribute($value)
    {
        return $value ?? "";
    }
    public function getUrlAttribute($value)
    {
        return $value ?? "";
    }
    public function getResetPasswordCodeAttribute($value)
    {
        return $value ?? "";
    }
    public function getCountryAttribute($value)
    {
        return $value ?? "";
    }
    public function getBusinessCategoryAttribute($value)
    {
        return $value ?? "";
    }
    public function getCompanyNameAttribute($value)
    {
        return $value ?? "";
    }
    public function getYearDobAttribute($value)
    {
        return $value ?? "";
    }
    public function getMonthDobAttribute($value)
    {
        return $value ?? "";
    }
    public function getDayDobAttribute($value)
    {
        return $value ?? "";
    }
}
