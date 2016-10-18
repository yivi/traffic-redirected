<?php
namespace Unir\V1\Rest\RedirectCollection;

use Unir\V1\Rest\Redirects\RedirectsResource;
use Zend\Stdlib\Parameters;
use Zend\Validator\ValidatorChain;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class RedirectCollectionResource extends AbstractResourceListener
{
    /** @var ValidatorChain origin_validator */
    protected $origin_validators;

    /** @var ValidatorChain origin_validator */
    protected $target_validators;

    /** @var  RedirectsResource redirects_resource */
    protected $redirects_resource;

    public function __construct($redirects_resource, $origin_validator, $target_validator)
    {
        $this->redirects_resource = $redirects_resource;
        $this->origin_validators  = $origin_validator;
        $this->target_validators  = $target_validator;

    }

    public function setOriginValidators($validator)
    {
        $this->origin_validators = $validator;
    }

    public function setTargetValidators($validator)
    {
        $this->origin_validators = $validator;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $inputFilter = $this->getInputFilter();
        $data        = $inputFilter->getValue('dataset');
        $fallos      = 0;
        $log_errores = null;
        $exitos      = 0;
        $filename    = date('Ymd-His') . '_errors_' . $data['name'];

        $file = fopen($data['tmp_name'], 'r');

        while ($row = fgetcsv($file, 1024, ';', '"', '\\')) {

            $redirect_type = isset($row[2]) && $row[2] ? $row[2] : 1;
            $context       = [
                'redirect_type' => $redirect_type
            ];

            if ($this->origin_validators->isValid($row[0], $context) && $this->target_validators->isValid($row[1], $context)) {

                $params = new Parameters(['target' => $row[1], 'origin' => $row[0], 'redirect_type' => $redirect_type]);
                $this->redirects_resource->create($params);

                $exitos++;
            } else {
                if (!$log_errores) {
                    $log_errores = fopen('./data/uploads/' . $filename, 'a');
                }
                $fallos++;
                fputcsv($log_errores, $row, ';', '"', '\\');

            }
        }

        unlink($data['tmp_name']);

        return ['fallos' => $fallos, 'report' => $filename];

    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {

        return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        return new ApiProblem(405, 'The GET method has not been defined for individual resources');
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        return new ApiProblem(405, 'The GET method has not been defined for collections');
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($id, $data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Patch (partial in-place update) a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patchList($data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for collections');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    /**
     * Update a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($id, $data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }
}
