<?php

namespace App\Controller;

use App\Logger\SiteLogManager;
use App\Service\MessageGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SyncSamsungHealthController extends AbstractController
{

    private $logManager;

    /**
     * @Route("/sync/samsung/health", name="sync_samsung_health", methods={"GET","HEAD"})
     */
    public function index_get()
    {
        return $this->render('sync_samsung_health/index.html.twig', [
            'controller_name' => 'SyncSamsungHealthController',
        ]);
    }

    /**
     * @Route("/sync/samsung/health/{data_set}", name="sync_samsung_health", methods={"POST"})
     * @param String $data_set
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index_post(String $data_set)
    {
        $request = Request::createFromGlobals();


        try {
            $path = sys_get_temp_dir() . '/shealth';
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        if (!empty($path)) {
            $fileSystem = new Filesystem();
            try {
                $fileSystem->mkdir($path);
                if ($fileSystem->exists($path . '/' . $data_set . '/shealth.txt')) {
                    $fileSystem->appendToFile($path . '/' . $data_set . '/shealth.txt', $request->getContent() . "\n");
                } else {
                    $fileSystem->dumpFile($path . '/' . $data_set . '/shealth.txt', $request->getContent() . "\n");
                }

            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at " . $exception->getPath();
            }
        }

//        $content = $this->getContentAsArray($request);

        return $this->json([
            'data_set' => $data_set,
            'path' => $path,
            'body' => $request->getContent(),
        ]);
    }

    /*protected function getContentAsArray(Request $request) {
        $content = $request->getContent();

        if(empty($content)){
            throw new BadRequestHttpException("Content is empty");
        }

        return new ArrayCollection(json_decode($content, true));
    }*/
}
