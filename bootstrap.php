<?php

// Is REST?
if (!COCKPIT_API_REQUEST) {
    return;
}

// Is cache request?
if (!isset($_REQUEST['_srcache'])) {
    return;
}

$this->on('before', function() {

    $hash = trim(COCKPIT_ADMIN_ROUTE.'/'.md5(serialize($_REQUEST)), '/').'.php';

    if ($file = $this->path("#tmp:apicache/{$hash}")) {

        $cache = include($file);

        if ($cache['eol'] < time()) {
            unlink($file);
        }

        $this->response->body = $cache['contents'];
        $this->response->flush();

        $this->stop();
    }
});

$this->on('after', function() {

    if ($this->response->status != 200) {
        return;
    }

    $hash = trim(COCKPIT_ADMIN_ROUTE.'/'.md5(serialize($_REQUEST)), '/').'.php';

    $this->filestorage->put("tmp://apicache/{$hash}", '<?php return '.var_export([
        'eol' => (time() + $this->retrieve('config/cache/duration', 60)),
        'contents' => $this->response->body
    ], true ).';');
});
