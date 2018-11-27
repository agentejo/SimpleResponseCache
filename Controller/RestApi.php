<?php
namespace SimpleResponseCache\Controller;

class RestApi extends \LimeExtra\Controller {

    public function invalidate() {

        $path = $this->param('path', '');
        $user = $this->module('cockpit')->getUser();

        if ($user && !$this->module('cockpit')->isSuperAdmin()) {
            return $this->stop(['error' => 'Unauthorized'], 401);
        }

        if (!$this->module('simpleresponsecache')->clear($path)) {
            $this->stop(['success' => false, 'message' => 'Path does not exist!'], 412);
        }

        return ['success' => true];
    }
}
