<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 12/15/17
 * Time: 3:34 PM
 */

namespace App\Repository\Services;


use App\Exceptions\CoreException;
use App\Exceptions\GeneralException;
use App\Project;
use App\Repository\Services\Core\PMCoreService;
use Illuminate\Http\Request;

class ProjectService
{
    protected $pmService;


    public function __construct(PMCoreService $pmService)
    {
        $this->pmService = $pmService;
    }

    /**
     * @param string $name
     * @param string $description
     * @param string $owner
     * @return Project
     * @throws CoreException
     */
    public function create(string $name, string $description, string $owner)
    {
        $project = Project::create([
            'name' => $name,
            'description' => $description,
            'active' => true,
        ]);
        $this->pmService->create($project->_id, $owner);
        return $project;
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return Project
     */
    public function update(Project $project, string $name, string $description)
    {
        if ($name)
            $project->name = $name;
        if ($description)
            $project->description = $description;
        $project->save();

        return $project;
    }


    /**
     * @param Project $project
     * @param array $aliases
     * @throws GeneralException
     */
    public function setAliases(Project $project, $aliases)
    {
        if (!$aliases || !$this->validateAlias($aliases))
            throw new GeneralException('لطفا اطلاعات را درست وارد کنید.', GeneralException::VALIDATION_ERROR);
        $project['aliases'] = $aliases;
        $project->save();
    }


    private function validateAlias($aliases)
    {
        foreach ($aliases as $a)
            if (gettype($a) !== 'string' && gettype($a) !== 'integer')
                return false;
        return true;
    }
}
