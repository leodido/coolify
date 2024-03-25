<?php

namespace App\Actions\Database;

use App\Models\ServiceDatabase;
use App\Models\StandaloneMariadb;
use App\Models\StandaloneMongodb;
use App\Models\StandaloneMysql;
use App\Models\StandalonePostgresql;
use App\Models\StandaloneRedis;
use Lorisleiva\Actions\Concerns\AsAction;

class StopDatabaseProxy
{
    use AsAction;

    public function handle(StandaloneRedis|StandalonePostgresql|StandaloneMongodb|StandaloneMysql|StandaloneMariadb|ServiceDatabase $database)
    {
        $server = data_get($database, 'destination.server');
        $uuid = $database->uuid;
        if ($database->getMorphClass() === 'App\Models\ServiceDatabase') {
            $uuid = $database->service->uuid;
            $server = data_get($database, 'service.server');
        }
        instant_remote_process(["docker rm -f {$uuid}-proxy"], $server);
        $database->is_public = false;
        $database->save();
    }
}
