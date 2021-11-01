<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\RealState;

class RealStateController extends Controller
{

    private $realState;

    public function __construct(RealState $realState)
    {
        $this->realState = $realState;
    }

    public function index()
    {

        $realState = $this->realState->with('categories', 'user')->paginate(10);

        return response()->json($realState, 200);
    }

    public function show($slug)
    {
        try {

            $realState = $this->realState->with('categories', 'user', 'photos')->where('slug', '=', $slug)->firstOrFail();

            return response()->json([
                'data' => $realState
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }
}
