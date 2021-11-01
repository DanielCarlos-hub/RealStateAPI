<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\RealState;
use App\RealStatePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RealStatePhotoController extends Controller
{

    private $realState;
    private $realStatePhoto;

    /**
     * @param RealState $realState
     * @param RealStatePhoto $realStatePhoto
     */
    public function __construct( RealState $realState, RealStatePhoto $realStatePhoto )
    {
        $this->realState = $realState;
        $this->realStatePhoto = $realStatePhoto;
    }

    public function index($realStateId)
    {
        $photos = $this->realStatePhoto->where('real_state_id', $realStateId)->get();

        return response()->json($photos, 200);
    }

    public function store($realStateId, Request $request)
    {
        $images = $request->file('images');

        try{
            $realState = $this->realState->findOrFail($realStateId);

            if($images){
                $imagesUploaded = [];

                foreach($images as $image){
                    $path = $image->store('images', 'public');
                    $imagesUploaded[] = ['photo' => $path];
                }

                $realState->photos()->createMany($imagesUploaded);

            }

            return response()->json([
                'data' => [
                    'msg' => 'Novas fotos adicionadas ao imovel '. $realState->code
                ]
            ], 200);

        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function show($realStateId, $photoId)
    {
        try {

            $realState = $this->realState->findOrFail($realStateId);
            $photo = $realState->photos()->find($photoId);

            return response()->json([
                'data' => $photo
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function update($realStateId, $photoId)
    {

        try {
            $photo = $this->realStatePhoto
            ->where('real_state_id', $realStateId)
            ->where('is_thumb', true)->first();

            if($photo)
                $photo->update(['is_thumb' => false]);

            $photo = $this->realStatePhoto->find($photoId);
            $photo->update(['is_thumb' => true]);

            return response()->json([
                'data' => [
                    'msg' => 'Photo atualizada com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function destroy($realStateId, $photoId)
    {
        try {

            $realState = $this->realState->findOrFail($realStateId);
            $photo = $realState->photos()->findOrFail($photoId);

            if($photo->is_thumb) {
                $message = new ApiMessages('Não é possível remover uma foto Thumb');
                return response()->json($message->getMessage(), 401);
            }

            if($photo){
                Storage::disk('public')->delete($photo->photo);
                $photo->delete();
            }

            return response()->json([
                'data' => [
                    'msg' => 'Foto removida com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }
}
