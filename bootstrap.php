<?php


$this->module('simpleresponsecache')->extend([

    'clear' => function($path = '') {

        $path = str_replace('..', '', $path);
        $path = $this->app->path("#tmp:apicache".($path ? "/{$path}":''));

        if ($path) {
            $this->app->helper('fs')->delete($path);
        }

        return false;
    }
]);


// Is REST?
if (COCKPIT_API_REQUEST) {
    include(__DIR__.'/api.php');
}

// invalidate cache on content update
$this->on('collections.save.after', function($name) {
    $this->module('simpleresponsecache')->clear("api/collections/get/{$name}");
});

$this->on('singleton.saveData.after', function($singleton) {
    $this->module('simpleresponsecache')->clear("api/singletons/get/{$singleton['name']}");
});
