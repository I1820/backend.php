<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 12/15/17
 * Time: 3:34 PM
 */

namespace App\Repository\Services;


use App\Exceptions\ProjectException;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectService
{

    /**
     * @param Request $request
     * @return void
     * @throws ProjectException
     */
    public function validateCreateProject(Request $request)
    {
        $messages = [
            'name.required' => 'لطفا نام پروژه را وارد کنید',
            'description.required' => 'لطفا توضیحات را درست وارد کنید',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ], $messages);

        if ($validator->fails())
            throw new  ProjectException($validator->errors()->first(), ProjectException::C_GE);
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function insertProject(Request $request)
    {
        return Project::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
        ]);
    }

}