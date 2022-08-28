<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

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
        $sortBy = $request->input("sort-by", "fullname");
        $sortDir = $request->input("sort-dir", "asc");
        $perPage = $request->input("per-page", 20);
        $search = $request->input("search", null);
        $models = Member::orderBy($sortBy, $sortDir);

        if ($search) {
            $models->where('fullname', 'like', '%' . $search . '%');
            $models->orWhere('nik', 'like', $search . '%');
        }

        $models = $models->paginate($perPage);

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
        $this->validate($request, Member::getDefaultValidator());
        $model = Member::create([
            "fullname" => $request->input("fullname"),
            "nik" => $request->input("nik"),
            "address" => $request->input("address"),
            "phone_number" => $request->input("phone_number"),
            "email" => $request->input("email"),
            "status" => $request->input("status"),
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
        $this->validate($request, Member::getDefaultValidator());

        $model = Member::findOrFail($id);

        $model->update([
            "fullname" => $request->input("fullname"),
            "nik" => $request->input("nik"),
            "address" => $request->input("address"),
            "phone_number" => $request->input("phone_number"),
            "email" => $request->input("email"),
            "status" => $request->input("status"),
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
        $sortBy = $request->input("sort-by", "fullname");
        $sortDir = $request->input("sort-dir", "asc");
        $perPage = $request->input("per-page", 20);
        $search = $request->input("search", null);
        $models = Member::onlyTrashed()->orderBy($sortBy, $sortDir);

        if ($search) {
            $models->where('fullname', 'like', '%' . $search . '%');
            $models->orWhere('nik', 'like', $search . '%');
        }
        $models = $models->paginate($perPage);

        return response()->json($models);
    }

}
