<?php


namespace App\Repository\Services\Core;


use App\Exceptions\CoreException;
use Ixudra\Curl\CurlService;

abstract class AbstractCoreService
{
    /**
     * @var CurlService
     */
    protected $curlService;

    public function __construct(CurlService $curlService)
    {
        $this->curlService = $curlService;
    }

    /**
     * @return string
     */
    protected abstract function url();

    /**
     * General fetch function for core services communication that handles errors
     * based on Echo Error Structure
     * @param string $path
     * @param array $data
     * @param string $method
     * @return array
     * @throws CoreException
     */
    protected function fetch(string $path, array $data, string $method)
    {
        $url = $this->url() . $path;

        $response = $this->curlService->to($url)
            ->withData($data)
            ->returnResponseObject()
            ->asJsonRequest()
            ->asJsonResponse(true)
            ->withTimeout('60');
        $new_response = null;
        switch ($method) {
            case 'post':
                $new_response = $response->post();
                break;
            case 'delete':
                $new_response = $response->delete();
                break;
            case 'put':
                $new_response = $response->put();
                break;
            case 'get':
            default:
                $new_response = $response->get();
                break;
        }

        if ($new_response->status == 0) {
            throw new CoreException(CoreException::M_CONNECTIVITY, 500);
        }
        if ($new_response->status == 200) {
            return $new_response->content ?: [];
        }
        if ($new_response->status != 200) {
            if ($new_response->content) {
                throw new CoreException(
                    $new_response->content->message ?: CoreException::M_UNKNOWN,
                    $new_response->status
                );
            } else {
                throw new CoreException(CoreException::M_CONNECTIVITY, $new_response->status);
            }
        }
    }
}