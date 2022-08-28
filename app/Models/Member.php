<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *   schema="Member",
 *   @OA\Property(property="fullname", type="string"),
 *   @OA\Property(property="nik", type="string"),
 *   @OA\Property(property="address", type="string"),
 *   @OA\Property(property="phone_number", type="string"),
 *   @OA\Property(property="email", type="string"),
 *   @OA\Property(property="status", type="integer"),
 * )
 */
class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fullname', "nik", "address", "phone_number", 'email', "status",
    ];

    public static function getDefaultValidator()
    {
        return [
            "fullname" => "required|string|max:100",
            "nik" => "required|string|max:32",
            "address" => "required|string|max:200",
            "phone_number" => "required|string|max:20",
            "email" => "string|max:200",
            "status" => "required|integer",
        ];
    }
}
