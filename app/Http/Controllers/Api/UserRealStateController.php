<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\RealStateRequest;
use Illuminate\Support\Facades\DB;

class UserRealStateController extends Controller
{

    public function index()
    {
        $realStates = auth('api')->user()->real_state();

        return response()->json($realStates->paginate(10), 200);
    }

    public function show($id)
    {
        try {

            $realState = auth('api')->user()->real_state()->with('photos')->findOrFail($id);

            return response()->json([
                'data' => $realState
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function store(RealStateRequest $request)
    {
        $data = $request->only(['title', 'description', 'content', 'price', 'bedrooms', 'bathrooms', 'garages', 'property_area', 'total_property_area']);

        $categories = $request->categories;
        $images = $request->file('images');

        DB::beginTransaction();

        try {

            $realState = auth('api')->user()->real_state()->create($data);

            if(isset($categories) && count($categories)){
                $realState->categories()->sync($categories);
            }

            if($images){
                $imagesUploaded = [];

                foreach($images as $image){
                    $path = $image->store('images', 'public');
                    $imagesUploaded[] = ['photo' => $path, 'is_thumb' => false ];
                }

                $realState->photos()->createMany($imagesUploaded);

            }

            DB::commit();
            return response()->json([
                'data' => [
                    'msg' => 'Imóvel cadastrado com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {

            DB::rollBack();
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function update(RealStateRequest $request, $id)
    {
        $data = $request->all();

        try {

            $realState = $this->realState->findOrFail($id);
            $realState->update($data);

            if(isset($data['categories']) && count($data['categories'])){
                $realState->categories()->sync($data['categories']);
            }

            return response()->json([
                'data' => [
                    'msg' => 'Imóvel atualizado com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function destroy($id)
    {

        try {

            $realState = auth('api')->user()->real_state()->findOrFail($id);
            $realState->delete();

            return response()->json([
                'data' => [
                    'msg' => 'Imóvel removido com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }
}
