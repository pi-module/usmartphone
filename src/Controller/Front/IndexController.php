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

class IndexController extends ActionController
{
    public function indexAction()
    {
        // Set return array
        $return = array(
            'active' => $this->config('active_login'),
            'message' => '',
        );
        // json output
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return $return;
    }

    public function loginAction()
    {
        // Set return array
        $return = array(
            'message' => '',
            'login' => 0,
            'identity' => '',
            'userid' => 0,
            'sessionid' => '',
        );
        // Check user login from allowed or not
        if ($this->config('active_login')) {
            // Check post array set or not
            if (!$this->request->isPost()) {
                $return['message'] = __('Invalid input please try again');
            } else {
                // Get from post
                $post = $this->request->getPost();
                $identity = $post['identity'];
                $credential = $post['credential'];
                // Set field
                $field = 'identity';
                if (Pi::service('module')->isActive('user')) {
                    $config = Pi::service('registry')->config->read('user');
                    $field = $config['login_field'];
                }
                // Try login
                $result = Pi::service('authentication')->authenticate(
                    $identity,
                    $credential,
                    $field
                );
                $result = $this->verifyResult($result);
                // Check login is valid
                if ($result->isValid()) {
                    $uid = (int)$result->getData('id');
                    // Bind user information
                    if (Pi::service('user')->bind($uid)) {
                        Pi::service('session')->setUser($uid);
                        $rememberMe = 14 * 86400;
                        Pi::service('session')->manager()->rememberme($rememberMe);
                        // Unset login session
                        if (isset($_SESSION['PI_LOGIN'])) {
                            unset($_SESSION['PI_LOGIN']);
                        }
                        // Set user login event
                        $args = array(
                            'uid' => $uid,
                            'remember_time' => $rememberMe,
                        );
                        Pi::service('event')->trigger('user_login', $args);
                        // Get user information
                        $user = Pi::model('user_account')->find($uid)->toArray();
                        // Set return array
                        $return['message'] = __('You have logged in successfully');
                        $return['login'] = 1;
                        $return['identity'] = $user['identity'];
                        $return['userid'] = $user['id'];
                        $return['sessionid'] = Pi::service('session')->getId();
                    } else {
                        $return['message'] = __('Bind error');
                    }
                } else {
                    $return['message'] = __('Invalid input please try again');
                }
            }
        }
        // json output
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return $return;
    }

    public function logoutAction()
    {
        // Get user id
        $uid = Pi::user()->getId();
        // Logout user actions
        Pi::service('session')->manager()->destroy();
        Pi::service('user')->destroy();
        Pi::service('event')->trigger('logout', $uid);
        // Set retrun array
        $return = array(
            'message' => __('You logged out successfully.'),
            'logout' => 1,
        );
        // json output
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return $return;
    }

    public function checkAction()
    {
        // Check user login from allowed or not
        if ($this->config('active_login')) {
            // Get session id
            $id = $this->params('id', '');
            // Check id set or not
            if (!empty($id)) {
                // Start session
                $session = Pi::model('session')->find($id);
                if ($session) {
                    // Old method for pi 2.4.0
                    /* 
                    session_id($id);
                    Pi::service('session')->manager()->start();
                    */
                    // New method for pi 2.5.0
                    $session = $session->toArray();
                    Pi::service('session')->manager()->start(false, $session['id']);
                }
            }
            // Check user has identity
            if (Pi::service('user')->hasIdentity()) {
                $return = array(
                    'check' => 1,
                    'uid' => Pi::user()->getId(),
                    'identity' => Pi::user()->getIdentity(),
                    'sessionid' => Pi::service('session')->getId(),
                );
            } else {
                $return = array(
                    'check' => 0,
                    'uid' => Pi::user()->getId(),
                    'identity' => Pi::user()->getIdentity(),
                    'sessionid' => Pi::service('session')->getId(),
                );
            }
        } else {
            // Set empty return
            $return = array(
                'check' => 0,
                'uid' => 0,
                'identity' => '',
                'sessionid' => '',
            );
        }
        // json output
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return $return;
    }

    protected function verifyResult(Result $result)
    {
        return $result;
    }
}