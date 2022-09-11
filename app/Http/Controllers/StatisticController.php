<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class StatisticController extends Controller
{

    /**
     * @OA\Get(
     *   tags={"Statistic"},
     *   path="/api/v1/statistic/member-by-status",
     *   summary="Get total member, group by status_id",
     *   @OA\Response(response=200, description="OK"),
     * )
     */
    public function memberByStatus(Request $request)
    {

        $collection = Member::groupBy('status_id')
            ->selectRaw('status_id, count(*) as total')
            ->get();

        return response()->json($collection);
    }

    /**
     * @OA\Get(
     *   tags={"Statistic"},
     *   path="/api/v1/statistic/member-by-gender",
     *   summary="Get member stats by gender",
     *   @OA\Response(response=200, description="OK"),
     * )
     */
    public function memberByGender(Request $request)
    {

        $collection = \DB::table(function ($query) {
            $query->from("members")
                ->selectRaw("case when substring(nik, 7,2)>40 then "
                    . "'M' else 'F' end as gender");
        }, "x")
            ->selectRaw('gender, count(*) as total')
            ->groupBy("gender")
            ->get();
        return response()->json($collection);
    }

    /**
     * @OA\Get(
     *   tags={"Statistic"},
     *   path="/api/v1/statistic/member-by-age",
     *   summary="Get member stats by age",
     *   @OA\Response(response=200, description="OK"),
     * )
     */
    public function memberByAge(Request $request)
    {

        $collection = \DB::table(function ($query) {
            $query->from("members")
                ->selectRaw("CASE WHEN CONVERT(substring(nik, 11,2), INT)<DATE_FORMAT(CURDATE(), '%y') THEN "
                    . "CONVERT(substring(nik, 11,2), INT)+2000 "
                    . "ELSE CONVERT(substring(nik, 11,2), INT)+1900 "
                    . "END AS yearOfBirth");

        }, "x")
            ->selectRaw('Year(CURDATE()) - x.yearOfBirth as age, x.yearOfBirth,  count(*) as total')
            ->groupBy("age", "x.yearOfBirth")
            ->get();

        return response()->json($collection);
    }

}
