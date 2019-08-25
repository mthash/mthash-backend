<?php
namespace MtHash\Controller;
class OopsController extends AbstractController
{
    public function getRestart()
    {
        $handler    = new \SeederTask();
        $handler->restartAction();

        $this->webResponse(200);
    }
}