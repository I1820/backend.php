<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 19/1/18
 * Time: 2:50 PM
 */

namespace App\Repository\Services;


use App\Codec;
use App\Exceptions\CodecException;
use App\Project;
use App\Thing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CodecService
{

    /**
     * @param Request $request
     * @return void
     * @throws CodecException
     */
    public function validateCreateCodec(Request $request)
    {
        $messages = [
            'name.required' => 'لطفا نام کدک را وارد کنید',
            'code.required' => 'لطفا کدک را وارد کنید',

        ];

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'name' => 'required', 'string', 'max:255'
        ], $messages);

        if ($validator->fails())
            throw new  CodecException($validator->errors()->first(), CodecException::C_GE);
    }

    /**
     * @param Request $request
     * @param Thing $thing
     * @return void
     */
    public function insertCodec(Request $request, Thing $thing)
    {
        $user = Auth::user();
        $codec = Codec::create([
            'name' => $request->get('name'),
            'code' => $request->get('code')
        ]);
        $thing->codec()->save($codec);
        $user->codecs()->save($codec);
        $codec->thing()->associate($thing);
        $codec->user()->associate($user);
        return $codec;
    }


    /**
     * @param Request $request
     * @param Thing $thing
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function updateCodec(Request $request, Thing $thing)
    {
        $data = $request->only(['name', 'code']);
        $codec = $thing->codec()->first();
        if (!$codec)
            return $this->insertCodec($request, $thing);
        $codec->code = $data['code'];
        $codec->name = $data['name'];
        $codec->save();
        return $codec;
    }
}