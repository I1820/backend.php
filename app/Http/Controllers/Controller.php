<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="I1820 API Backend",
 *      description="The Glue",
 *      @OA\Contact(
 *          name="Parham Alvani",
 *          email="parham.alvani@gmail.com"
 *      ),
 *      @OA\License(
 *         name="GPLv3",
 *         url="https://www.gnu.org/licenses/gpl-3.0.html"
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
