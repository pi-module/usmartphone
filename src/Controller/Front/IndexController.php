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
use Pi\Authentication\Result;
use Pi\Mvc\Controller\ActionController;

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
        // Check if already logged in
        if (Pi::service('user')->hasIdentity()) {
            // Get user
            $user = Pi::user()->get(Pi::user()->getId(), array(
                'id', 'identity', 'name', 'email'
            ));
            // Set result
            $return = array(
                'check' => 1,
                'uid' => $user['id'],
                'identity' => $user['identity'],
                'email' => $user['email'],
                'name' => $user['name'],
                'avatar' => Pi::service('user')->avatar($user['id'], 'large', false),
                'sessionid' => Pi::service('session')->getId(),
                'message' => __('You are login to system before'),
            );
        } else {
            // Check user login from allowed or not
            if ($this->config('active_login')) {
                // Check post array set or not
                if (!$this->request->isPost()) {
                    // Set result
                    $return = array(
                        'check' => 0,
                        'uid' => Pi::user()->getId(),
                        'identity' => Pi::user()->getIdentity(),
                        'email' => '',
                        'name' => '',
                        'avatar' => '',
                        'sessionid' => Pi::service('session')->getId(),
                        'message' => __('Post request not set'),
                    );
                } else {
                    // Get from post
                    $post = $this->request->getPost();
                    $identity = $post['identity'];
                    $credential = $post['credential'];
                    // Do login
                    $return = $this->doLogin($identity, $credential);
                }
            } else {
                // Set result
                $return = array(
                    'check' => 0,
                    'uid' => Pi::user()->getId(),
                    'identity' => Pi::user()->getIdentity(),
                    'email' => '',
                    'name' => '',
                    'avatar' => '',
                    'sessionid' => Pi::service('session')->getId(),
                    'message' => __('Login not active'),
                );
            }
        }

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
                // Get user
                $user = Pi::user()->get(Pi::user()->getId(), array(
                    'id', 'identity', 'name', 'email'
                ));
                // Set result
                $return = array(
                    'check' => 1,
                    'uid' => $user['id'],
                    'identity' => $user['identity'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'avatar' => Pi::service('user')->avatar($user['id'], 'large', false),
                    'sessionid' => Pi::service('session')->getId(),
                );
            } else {
                $return = array(
                    'check' => 0,
                    'uid' => Pi::user()->getId(),
                    'identity' => Pi::user()->getIdentity(),
                    'email' => '',
                    'name' => '',
                    'avatar' => '',
                    'sessionid' => Pi::service('session')->getId(),
                );
            }
        } else {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Login not active'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // json output
        return $return;
    }

    public function profileAction()
    {
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
            if (Pi::service('user')->hasIdentity()) {
                $fields = array(
                    'id', 'identity', 'name', 'email', 'first_name', 'last_name', 'id_number', 'phone', 'mobile',
                    'address1', 'address2', 'country', 'state', 'city', 'zip_code', 'company', 'company_id', 'company_vat',
                    'your_gift', 'your_post', 'company_type', 'latitude', 'longitude',
                );
                // Find user
                $uid = Pi::user()->getId();
                $return = Pi::user()->get($uid, $fields);
                $return['avatar'] = Pi::service('avatar')->get($return['id'], 'large', false);
                $return['uid'] = $uid;
                $return['check'] = 1;
                $return['sessionid'] = Pi::service('session')->getId();
            } else {
                $return = array(
                    'check' => 0,
                    'uid' => Pi::user()->getId(),
                    'identity' => Pi::user()->getIdentity(),
                    'email' => '',
                    'name' => '',
                    'avatar' => '',
                    'sessionid' => Pi::service('session')->getId(),
                );
            }
        } else {
            // Set empty return
            /* $return = array(
                'check' => 0,
                'uid' => 0,
                'identity' => '',
                'email' => '',
                'name' => '',
                'avatar' => '',
                'sessionid' => '',
            ); */
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Login not active'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // json output
        return $return;
    }

    public function doLogin($identity, $credential)
    {
        // Set return array
        $return = array(
            'message' => '',
            'login' => 0,
            'identity' => '',
            'email' => '',
            'name' => '',
            'avatar' => '',
            'uid' => 0,
            'userid' => 0,
            'sessionid' => '',
            'error' => 0,
            'check' => 0
        );

        // Set field
        $field = 'identity';
        if (Pi::service('module')->isActive('user')) {
            $config = Pi::service('registry')->config->read('user');
            $field = $config['login_field'];
            $field = array_shift($field);
        }

        // try login
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
                //$user = Pi::model('user_account')->find($uid)->toArray();
                $user = Pi::user()->get($uid, array(
                    'id', 'identity', 'name', 'email'
                ));
                // Set return array
                $return['message'] = __('You have logged in successfully');
                $return['login'] = 1;
                $return['identity'] = $user['identity'];
                $return['email'] = $user['email'];
                $return['name'] = $user['name'];
                $return['avatar'] = Pi::service('user')->avatar($user['id'], 'medium', false);
                $return['uid'] = $user['id'];
                $return['userid'] = $user['id'];
                $return['sessionid'] = Pi::service('session')->getId();
                $return['check'] = 1;
            } else {
                $return['error'] = 1;
                $return['message'] = __('Bind error');
            }
        } else {
            $return['error'] = 1;
            $return['message'] = __('Authentication is not valid');
        }

        return $return;
    }

    protected function verifyResult(Result $result)
    {
        return $result;
    }
}