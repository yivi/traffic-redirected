<?php

namespace Unir\Controller;

use Zend\Http\Headers;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;

class RedirectCollectionImportLogController extends AbstractActionController
{
    public function indexAction()
    {
        $requested_file = $this->params()->fromRoute('filename');
        $path           = './data/uploads';
        $fullpath       = $path . '/' . $requested_file;

        if ($requested_file && file_exists($fullpath)) {

            $response = new Stream();
            $response->setStream(fopen($fullpath, 'r'));
            $response->setStatusCode(200);
            $response->setStreamName(basename($fullpath));
            $headers = new Headers();
            $headers->addHeaders(array(
                'Content-Disposition' => 'attachment; filename="' . basename($fullpath) . '"',
                'Content-Type'        => 'application/octet-stream',
                'Content-Length'      => filesize($fullpath),
                'Expires'             => '@0', // @0, because zf2 parses date as string to \DateTime() object
                'Cache-Control'       => 'must-revalidate',
                'Pragma'              => 'public'
            ));
            $response->setHeaders($headers);
            return $response;
        }
    }

}