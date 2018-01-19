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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CodecService
{

    /**
     * @param Request $request
     * @param Project $project
     * @return void
     * @throws CodecException
     */
    public function validateCreateCodec(Request $request, Project $project)
    {
        $messages = [
            'name.required' => 'لطفا نام کدک را وارد کنید',
            'name.required' => 'لطفا نام کدک را وارد کنید',
            'name.unique' => 'نام قبلا وجود دارد',

        ];

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('codecs')->where(function ($query) use ($project) {
                    return $query->where('project_id', $project->id);
                })
            ]
        ], $messages);

        if ($validator->fails())
            throw new  CodecException($validator->errors()->first(), CodecException::C_GE);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function insertCodec(Request $request, Project $project)
    {
        return Codec::create([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'project_id' => $project->id,
            'user_id' => Auth::user()->id,
        ]);
    }

    /**
     * @param Request $request
     * @return void
     * @throws CodecException
     */
    public function validateUpdateCodec(Request $request)
    {
        $messages = [
            'name.required' => 'لطفا نام کدک را وارد کنید',
            'code.required' => 'لطفا کدک را وارد کنید',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required',
        ], $messages);

        if ($validator->fails())
            throw new  CodecException($validator->errors()->first(), CodecException::C_GE);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return $this|\Illuminate\Database\Eloquent\Model
     * @throws CodecException
     */
    public function updateCodec(Request $request, Project $project)
    {
        $data = $request->only(['name', 'code']);
        $codec = $project->codecs()->where('name', $data['name'])->first();
        if (!$codec)
            throw new CodecException('Codec Not Found', CodecException::C_GE);
        $codec->code = $data['code'];
        $codec->save();
        return $codec;
    }
}