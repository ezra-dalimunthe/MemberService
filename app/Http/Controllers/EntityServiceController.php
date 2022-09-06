<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class EntityServiceController extends Controller
{
    /**
     * @OA\Get(
     *   tags={"Entity"},
     *   path="/api/v1/entity/member/{id}",
     *   summary="Show a member",
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(type="integer")
     *    ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(ref="#/components/schemas/Member")
     *     ),
     *   ),
     *   @OA\Response(response=404, description="Not Found",
     *       @OA\JsonContent(ref="#/components/schemas/ResourceNotFoundResponse")
     *   ),
     *   @OA\Response(response=403, description="User'rights is insufficient",
     *       @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
     *    ),
     * )
     */
    public function showMember(Request $request, $id)
    {
        $model = Member::withTrashed()->findOrFail($id);
        $model->setHidden(["created_at", "deleted_at", "updated_at"]);
        return response()->json(["member" => $model], 200);
    }

    /**
     * @OA\Get(
     *   tags={"Entity"},
     *   path="/api/v1/entity/members",
     *   summary="Get all member filtered by ids",
     *   @OA\Parameter(
     *      name="ids",
     *      in="query",
     *      required=true,
     *      @OA\Schema(type="string")
     *    ),
     *   @OA\Response(response=200, description="OK"),
     * )
     */
    public function showMembers(Request $request)
    {
        $ids = $request->input("ids");
        $ids = explode(",", $ids);
        $models = Member::withTrashed()->whereIn("id", $ids)
            ->get(["id", "fullname", "address", "email", "nik", "phone_number"]);
        return response()->json(["members" => $models], 200);

    }
}
