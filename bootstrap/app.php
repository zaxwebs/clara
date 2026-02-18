<?php

declare(strict_types=1);

use Clara\core\Application;

return Application::boot(BASE_PATH)
    ->withRoutes(BASE_PATH . '/config/routes.php');
