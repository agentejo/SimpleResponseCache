<?php


$this->on('cockpit.rest.init', function($routes) {
    $routes['rspc'] = 'SimpleResponseCache\\Controller\\RestApi';
});

$this->on('before', function() {

    // Is cache request?
    if (!$this->param('rspc') || preg_match('#^/api/rspc#i', $this['route'])) {
        return;
    }

    $hash = trim($this['route'].'/'.md5(serialize($this->request->request)), '/').'.php';
    $file = $this->path("#tmp:apicache/{$hash}");

    if ($file) {

        try {
            $cache = include($file);
        } catch(\Throwable $e) {
            @unlink($file);
            return;
        }

        if ($cache['eol'] < time()) {
            @unlink($file);
            return;
        }

        $this->response->headers[] = 'COCKPIT_RSP_CACHE: true';
        $this->response->mime = $cache['mime'] ?? 'text/html';
        $this->response->body = $cache['contents'];
        $this->response->flush();

        $this->trigger('simpleresponsecache.after');

        $this->stop();
    }

    $this->on('after', function() {

        if ($this->response->status != 200) {
            return;
        }

        $hash = trim(COCKPIT_ADMIN_ROUTE.'/'.md5(serialize($this->request->request)), '/').'.php';

        $this->filestorage->put("tmp://apicache/{$hash}", '<?php return '.var_export([
            'mime' => $this->response->mime,
            'eol' => (time() + $this->retrieve('config/responseCache/duration', 60)),
            'contents' => is_object($this->response->body) ? json_decode(json_encode($this->response->body), true) : $this->response->body
        ], true ).';');

    }, -2000);

}, 2000);
