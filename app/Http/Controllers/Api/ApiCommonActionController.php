<?php

namespace App\Http\Controllers\Api;

use App\Repository\Pos\BaseRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ApiCommonActionController extends ApiController
{
    private static $namespace = "App\Models\\";


    public function statusSwitch()
    {
        $v = Validator::make($input = Input::all(),
            [
                'entity' => 'required|string',
                'id'     => 'required|integer',
            ],
            [
                'entity.required' => 'entity不可為空白喔！',
                'id.required'     => 'id不可為空白喔！',
            ]
        );

        if ($v->fails()) {
            return $this->errorWrongArgs($v->errors());
        }

        $entity   = app::make(static::$namespace . $input['entity']);
        $id       = $input['id'];
        $instance = $entity::find($id);
        $status   = (int)!($instance->is_launch);

        BaseRepository::updateColumnById($entity, 'is_launch', $id, $status);
        $message = "{$input['entity']} : {$id} => " . $status;
        return $this->respondWithOKMessage($message);
    }


    public function updateOrder()
    {
        $v = Validator::make($input = Input::all(),
            [
                'entity' => 'required|string',
                'orders' => 'required|array',
            ],
            [
                'entity.required' => 'entity不可為空白喔！',
                'orders.required' => 'orders不可為空白喔！',
            ]
        );

        if ($v->fails()) {
            return $this->errorWrongArgs($v->errors());
        }

        $entity = app::make(static::$namespace . ucfirst($input['entity']));
        $orders = $input['orders'];

        foreach ($orders as $item) {
            BaseRepository::updateColumnById($entity, 'order', $item['id'], $item['order']);
        }
        return $this->respondWithOKMessage('ok');
    }

}
