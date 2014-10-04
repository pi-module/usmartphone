<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Usmartphone\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\Authentication\Result;
use Zend\Json\Json;

class IndexController extends ActionController
{
    public function loginAction()
    {
        $return = array(
            'message'   => '',
            'login'     => 0,
            'identity'  => '',
            'userid'    => 0,
            'sessionid' => '',
        );
            
        if (!$this->request->isPost()) {
            $return['message'] = __('Invalid input please try again');
        } else {
            $post = $this->request->getPost();
            $identity = $post['identity'];
            $credential = $post['credential'];
            $result = Pi::service('authentication')->authenticate($identity, $credential);
            $result = $this->verifyResult($result);
            if ($result->isValid()) {
                $uid = (int) $result->getData('id');
                $user = Pi::model('user_account')->find($uid)->toArray();
                // Set return array
                $return['message']    = __('You have logged in successfully');
                $return['login']      = 1;
                $return['identity']   = $user['identity'];
                $return['userid']     = $user['id'];
                $return['sessionid']  = Pi::service('session')->getId();
            } else {
                $return['message'] = __('Invalid input please try again');
            }
        }
        echo Json::encode($return);
        exit;
    }

    public function logoutAction()
    {
        $uid = Pi::user()->getId();
        Pi::service('session')->manager()->destroy();
        Pi::service('user')->destroy();
        Pi::service('event')->trigger('logout', $uid);

        $return = array(
            'message'   => __('You logged out successfully.'),
            'logout'    => 1,
        );
        $this->view()->setTemplate(false)->setLayout('layout-content');
        echo Json::encode($return);
        exit;
    }

    public function checkAction()
    {
        $id = $this->params('id', '');

        $return = array();

        if (!empty($id)) {
            Pi::service('session')->regenerateId($id);
        }

        if (Pi::service('user')->hasIdentity()) {
            $return = array(
                'check'     => 1,
                'uid'       => Pi::user()->getId(),
                'sessionid' => Pi::service('session')->getId(),
            );
        } else {
            $return = array(
                'check'     => 0,
                'uid'       => 0,
                'sessionid' => Pi::service('session')->getId(),
            );
        }

        $this->view()->setTemplate(false)->setLayout('layout-content');
        echo Json::encode($return);
        exit;
    }

    protected function verifyResult(Result $result)
    {
        return $result;
    }
}