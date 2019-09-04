<?php
namespace MtHash\Controller;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Dashboard\Dashboard;
use MtHash\Model\Dashboard\Overview;

class AdminController extends AbstractController
{
    private function checkAccess() : void
    {
        if (1 !== $this->currentUser->is_admin) throw new \Exception('Access denied');
    }

    public function getRestart()
    {
        $this->checkAccess();

        $handler    = new \SeederTask();
        $handler->restartAction();

        $this->webResponse(200);
    }

    public function getOverview()
    {
        $this->checkAccess();

        $overview   = new Overview();
        $this->webResponse($overview->getStaticData());
    }

    public function postOverview()
    {
        $this->checkAccess();
        $input  = $this->getInput();

        if (isset ($input['daily_revenue']))
        {
            $handler    = new Overview();
            $handler->updateDailyRevenue($input['daily_revenue']);
        }

        if (isset ($input['power']))
        {
            $handler    = new Overview();
            $handler->updatePool($input['power']);
        }

        $this->webResponse(['ok']);
    }
}