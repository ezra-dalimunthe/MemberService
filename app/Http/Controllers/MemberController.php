<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MemberController extends Controller
{
    /**
     * @OA\Get(
     *   tags={"Member"},
     *   path="/api/v1/members",
     *   summary="Member index",
     *    @OA\Parameter( name="page", in="query", required=false,
     *        description="expected page number", @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter( name="per-page", in="query", required=false,
     *        description="number of items on page", @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter( name="search", in="query", required=false,
     *        description="search by keyword", @OA\Schema(type="string")
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="OK",
     *        @OA\JsonContent(
     *            allOf={ @OA\Schema(ref="#/components/schemas/data-pagination") },
     *            @OA\Property(
     *                property="models",
     *                type="array",
     *                @OA\Items(
     *                    allOf={
     *                        @OA\Schema(ref="#/components/schemas/AutoIncrement"),
     *                        @OA\Schema(ref="#/components/schemas/Member"),
     *                    }
     *                ),
     *            )
     *        ),
     *    ),
     *    @OA\Response(response=403, description="Forbidden",
     *        @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
     *    ),
     *    @OA\Response(response=404, description="Not Found",
     *        @OA\JsonContent(ref="#/components/schemas/ResourceNotFoundResponse")
     *    )
     * )
     */
    public function index(Request $request)
    {
        $models = $this->query($request, 0);

        return response()->json($models);
    }
    /**
     * @OA\Post(
     *   tags={"Member"},
     *   path="/api/v1/member",
     *   summary="Member store",
     *    @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *       allOf={
     *          @OA\Schema(ref="#/components/schemas/Member")
     *       },
     *      )
     *    ),
     *    @OA\Response(
     *      response=201,
     *      description="OK",
     *      @OA\JsonContent(
     *       allOf={
     *          @OA\Schema(ref="#/components/schemas/data_manipulation_response"),
     *       },
     *       @OA\Property(property="model", type="object",
     *          allOf={
     *            @OA\Schema(ref="#/components/schemas/AutoIncrement"),
     *             @OA\Schema(ref="#/components/schemas/Member"),
     *          }
     *       )
     *      )
     *    ),
     *    @OA\Response(response=403, description="Forbidden",
     *       @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
     *    )
     * )
     */
    public function store(Request $request)
    {
        $this->validate($request, Member::getDefaultValidator(),
            Member::getDefaultValidatorMessage());
        $model = Member::create([
            "fullname" => $request->input("fullname"),
            "nik" => $request->input("nik"),
            "address" => $request->input("address"),
            "phone_number" => $request->input("phone_number"),
            "email" => $request->input("email"),
            "status_id" => $request->input("status_id"),
        ]);

        return response()->json([
            "message" => "New Member registered successfully!",
            "model" => $model,
        ], 201);
    }

    /**
     * @OA\Put(
     *   tags={"Member"},
     *   path="/api/v1/member/{id}",
     *   summary="Member update",
     *    @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(type="integer")
     *    ),
     *    @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *       allOf={
     *          @OA\Schema(ref="#/components/schemas/AutoIncrement"),
     *          @OA\Schema(ref="#/components/schemas/Member")
     *       },
     *      )
     *    ),
     *    @OA\Response(
     *      response=200,
     *      description="OK",
     *      @OA\JsonContent(
     *       allOf={
     *          @OA\Schema(ref="#/components/schemas/data_manipulation_response"),
     *       },
     *       @OA\Property(property="model", type="object",
     *          allOf={
     *            @OA\Schema(ref="#/components/schemas/AutoIncrement"),
     *             @OA\Schema(ref="#/components/schemas/Member"),
     *          }
     *       )
     *      )
     *    ),
     *    @OA\Response(response=403, description="Forbidden",
     *       @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
     *    )
     * )
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, Member::getUpdateValidator());

        $model = Member::findOrFail($id);

        $uniqueValidator = [];
        if ($model->nik != $request->input("nik")) {
            $uniqueValidator["nik"] = "unique:members,nik";
        }
        if ($model->phone_number != $request->input("phone_number")) {
            $uniqueValidator["phone_number"] = "unique:members,phone_number";
        }
        if ($model->email != $request->input("email")) {
            $uniqueValidator["email"] = "unique:members,email";
        }

        if (sizeOf($uniqueValidator) > 0) {
            $this->validate($request, $uniqueValidator, Member::getDefaultValidatorMessage());
        }

        $model->update([
            "fullname" => $request->input("fullname"),
            "nik" => $request->input("nik"),
            "address" => $request->input("address"),
            "phone_number" => $request->input("phone_number"),
            "email" => $request->input("email"),
            "status_id" => $request->input("status_id"),
        ]);
        return response()->json([
            "message" => "Member updated successfully!",
            "model" => $model,
        ], 200);
    }

    /**
     * @OA\Get(
     *   tags={"Member"},
     *   path="/api/v1/member/{id}",
     *   summary="Member show",
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
    public function show(Request $request, $id)
    {
        $model = Member::findOrFail($id);
        return response()->json(["member" => $model], 200);
    }

    /**
     * @OA\Delete(
     *   tags={"Member"},
     *   path="/api/v1/member/{id}",
     *   summary="Member destroy",
     *    @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(type="integer")
     *    ),
     *   @OA\Response(
     *     response=204,
     *     description="OK"
     *   ),
     *    @OA\Response(response=403, description="Forbidden",
     *       @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
     *    )
     * )
     */
    public function destroy(Request $request, $id)
    {
        $model = Member::find($id);
        if ($model) {
            $model->delete();
        }
        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *   tags={"Member"},
     *   path="/api/v1/members/deleted",
     *   summary="Show Deleted members",
     *    @OA\Parameter( name="page", in="query", required=false,
     *        description="expected page number", @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter( name="per-page", in="query", required=false,
     *        description="number of items on page", @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter( name="search", in="query", required=false,
     *        description="search by keyword", @OA\Schema(type="string")
     *    ),
     *   @OA\Response(response=200, description="OK"),
     * )
     */
    public function deleted(Request $request)
    {

        $models = $this->query($request, 1);
        return response()->json($models);
    }

    // $type= 0->normal, 1 -> only deleted, 2-> all (normal+deleted)
    private function query(Request $request, $type = 0)
    {
        $sortBy = $request->input("sort-by", "fullname");
        $sortDir = $request->input("sort-dir", "asc");
        $perPage = $request->input("per-page", 20);
        $search = $request->input("search", null);

        $models = null;

        switch ($type) {
            case 0:
                $models = new Member;
                break;
            case 1:
                $models = Member::onlyTrashed();
                break;
            case 2:
                $models = Member::withTrashed();
        }

        $models = $models->orderBy($sortBy, $sortDir);

        if ($search) {
            $params = json_decode($search, true);
            $field = strtolower(key($params));
            $value = current($params);
            switch ($field) {
                case "nik":
                    $models->where($field, 'like', $value . '%');
                    break;
                default:
                    $models->where($field, 'like', "%" . $value . '%');
            }

        }

        $models = $models->paginate($perPage);

        return $models;
    }
}
