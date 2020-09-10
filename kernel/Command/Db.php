<?php

namespace Kernel\Command;


use Kernel\ClassMap;
use Kernel\Command;

class Db extends Command
{
    private $database;

    public function check()
    {
        $this->connectDatabase(
            env_get('DB_USER'),
            env_get('DB_PASS'),
            env_get('DB_NAME'),
            env_get('DB_HOST', 'localhost'),
            env_get('DB_PORT', 3306),
        );
        $this->comment('Database Connection Successfully.');
    }

    public function migrate()
    {
        $namespace = 'App\\database\\migrations';
        $migration_classes = ClassMap::map($namespace, app_path('database/migrations'));
        foreach ($migration_classes as $class => $methods) {
            $obj = $namespace . "\\" . $class;
            $obj = new $obj;
            $obj->up();
            $this->comment("Table [$class] Created Successfully.");
        }

    }

    public function info()
    {
        $namespace = 'App\\database\\migrations';
        $migration_classes = ClassMap::map($namespace, app_path('database/migrations'));
        foreach ($migration_classes as $class => $methods) {
            $obj = $namespace . "\\" . $class;
            $obj = new $obj;
            $obj->up();
            $this->comment("Table [$class] Created Successfully.");
        }

    }

    public function seed():void
    {
        $folder = $this->parameters[0] ?? '';

        $namespace = 'App\\database\\seeds'.($folder?"\\$folder":"");
        $seed_classes = ClassMap::map($namespace, app_path('database/seeds/'.$folder));

        foreach ($seed_classes as $class => $methods) {
            $obj = $namespace . "\\" . $class;
            $obj = new $obj;
            $obj->run();
            $this->comment("Seed [$class] Executed Successfully.");
        }
    }

    public function rollback()
    {
        $namespace = 'App\\database\\migrations';
        $migration_classes = ClassMap::map($namespace, app_path('database/migrations'));
        $migration_classes = array_reverse($migration_classes);
        foreach ($migration_classes as $class => $methods) {
            $obj = $namespace . "\\" . $class;
            $obj = new $obj;
            $obj->down();
            $this->comment("Table [$class] Dropped Successfully.");
        }

    }

    public function renew(){
        $this->rollback();
        $this->migrate();
        $this->seed();
    }

    private function connectDatabase($user, $pass, $db_name, $host = 'localhost', $port = '3306')
    {
        $this->database = new \Kernel\DB($host, $port, $user, $pass, $db_name);
        $this->database->connect();
        if ($this->database->error) {
            $this->error("Database Connection Failed!");
        }
    }
}