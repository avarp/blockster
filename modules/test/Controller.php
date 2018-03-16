<?php
namespace modules\test;

class Controller extends \modules\Controller
{
    public function action_default($params)
    {
        if (core()->useCache('action_default', 90)) return;
        sleep(1);
        $this->view->data['time'] = time();
        $this->view->data['header'] = 'Header';
        return $this->view->render();
    }

    public function action_nesting_test($params)
    {
        core()->broadcastMessage('test', 'setHeader', 'Заголовок');
        $this->view->template = 'alternative.php';
        return $this->view->render();
    }

    public function acceptMessage($message, $param)
    {
        if ($message == 'setHeader') $this->view->data['header'] = $param;
    }
}