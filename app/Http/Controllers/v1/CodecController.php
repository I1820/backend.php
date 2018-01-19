<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\CodecException;
use App\Project;
use App\Repository\Helper\Response;
use App\Repository\Services\CodecService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CodecController extends Controller
{
    protected $codecService;

    public function __construct(CodecService $codecService)
    {
        $this->codecService = $codecService;
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return array
     * @throws CodecException
     */
    public function create(Request $request, Project $project)
    {
        $user = Auth::user();
        if ($project['owner']['id'] != $user->id)
            abort(404);

        $this->codecService->validateCreateCodec($request, $project);

        $codec = $this->codecService->insertCodec($request, $project);

        return Response::body(compact('codec'));
    }


    /**
     * @param Project $project
     * @return array
     */
    public function get(Project $project)
    {
        $user = Auth::user();
        if ($project['owner']['id'] != $user->id)
            abort(404);
        $codecs = $project->codecs()->get();

        return Response::body(compact('codecs'));
    }


    /**
     * @param Request $request
     * @param Project $project
     * @return array
     * @throws CodecException
     */
    public function update(Request $request, Project $project)
    {
        $user = Auth::user();
        if ($project['owner']['id'] != $user->id)
            abort(404);
        $this->codecService->validateUpdateCodec($request, $project);

        $codec = $this->codecService->updateCodec($request, $project);

        return Response::body(compact('codec'));
    }


}
