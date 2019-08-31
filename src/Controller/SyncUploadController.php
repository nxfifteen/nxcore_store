<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use /** @noinspection PhpUnusedAliasInspection */
    Symfony\Component\Routing\Annotation\Route;

class SyncUploadController extends AbstractController
{
    /**
     * @Route("/sync/upload", name="sync_upload", methods={"GET","HEAD"})
     */
    public function index()
    {
        return $this->render('sync_upload/index.html.twig', [
            'controller_name' => 'SyncUploadController',
        ]);
    }

    /**
     * @Route("/sync/upload/{service}/{data_set}", name="sync_upload_post", methods={"POST"})
     * @param String $service
     * @param String $data_set
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index_post(String $service, String $data_set)
    {
        $request = Request::createFromGlobals();

        try {
            $path = sys_get_temp_dir() . '/sync_upload_post';
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        if (!empty($path)) {
            $file = $path . '/' . $service . '_' . $data_set . '.txt';

            $fileSystem = new Filesystem();
            try {
                $fileSystem->mkdir($path);
                if ($fileSystem->exists($file)) {
                    $fileSystem->appendToFile($file, $request->getContent() . "\n");
                } else {
                    $fileSystem->dumpFile($file, $request->getContent() . "\n");
                }

            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at " . $exception->getPath();
            }
        }

        return $this->json([
            'data_set' => $data_set,
            'path' => $path,
            'body' => $request->getContent(),
        ]);
    }

}