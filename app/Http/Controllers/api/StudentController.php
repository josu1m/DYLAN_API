<?php

namespace App\Http\Controllers\api;

use App\classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Interfaces\StudentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    private StudentRepositoryInterface $studentRepositoryInterface;

    public function __construct(StudentRepositoryInterface $studentRepositoryInterface)
    {
        $this->studentRepositoryInterface = $studentRepositoryInterface;
    }
    public function index()
    {
        $data = $this->studentRepositoryInterface->getAll();
        return ApiResponseHelper::sendResponse(StudentResource::collection($data), '', 200);
    }
    public function show(string $id)
    {
        $student = $this->studentRepositoryInterface->getAll();
        return ApiResponseHelper::sendResponse(StudentResource::collection($student), '', 200);
    }

    public function store(StoreStudentRequest $request)
    {
        $data = [
            'name' => $request->name,
            'age' => $request->age
        ];
        DB::beginTransaction();
        try {
            $student = $this->studentRepositoryInterface->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(new StudentResource($student), 'Record create successful', 201);

        } catch (\Exception $ex) {
            DB::rollBack();
            $apiResponseHelper = new ApiResponseHelper();
            return $apiResponseHelper->rollback($ex);
        }
    }


    public function update(UpdateStudentRequest $request, string $id)
    {
        $data = [
            'name' => $request->name,
            'age' => $request->age
        ];
        DB::beginTransaction();
        try {
            $this->studentRepositoryInterface->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Record update successful', 200);

        } catch (\Exception $ex) {
            DB::rollBack();
            $apiResponseHelper = new ApiResponseHelper();
            return $apiResponseHelper->rollback($ex);
        }
    }

    public function destroy($id)
    {
        $this->studentRepositoryInterface->delete($id);
        return ApiResponseHelper::sendResponse(null, 'Registro Eliminado', 200);

    }
}
