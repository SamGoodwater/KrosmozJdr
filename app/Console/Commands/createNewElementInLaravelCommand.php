<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class createNewElementInLaravelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:newElement {elementType} {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Créer un nouveau élément (controller, resource, factory, models, ...) en créant l'ensemble des élements hébituels (pages, section, auth, modules)";

    const ELEMENT_TYPE_DEFAULT = 'controller';
    const BASE = [
        'page',
        'section',
    ];
    const AUTH = [
        'auth',
        'user',
    ];
    const MODULES = [
        'attribute',
        'campaign',
        'caoability',
        'classe',
        'condition',
        'consumable',
        'consumabletype',
        'item',
        'itemtype',
        'mob',
        'mobrace',
        'npc',
        'panoply',
        'resource',
        'resourcetype',
        'scenario',
        'shop',
        'specialization',
        'spell',
        'spelltype'
    ];
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('elementType');
        if ($type === null) {
            $type = self::ELEMENT_TYPE_DEFAULT;
        }
        $path = $this->argument('path');
        if (substr($path, -1) !== '/' && $path !== '') {
            $path .= '/';
        }

        $this->createBase($type, $path);
        $this->createAuth($type, $path);
        $this->createModules($type, $path);
    }

    private function createBase($type, $path = null)
    {
        foreach (self::BASE as $element) {
            $this->createElement($element, $type, $path);
        }
    }

    private function createAuth($type, $path = null)
    {
        foreach (self::AUTH as $element) {
            $this->createElement($element, $type, $path . 'Auth/');
        }
    }

    private function createModules($type, $path = null)
    {
        foreach (self::MODULES as $element) {
            $this->createElement($element, $type, $path . 'Modules/');
        }
    }

    private function createElement($element, $type, $path = null)
    {
        $element_name = ucfirst($element) . ucfirst($type);
        $this->call('make:' . $type, [
            'name' => $path . $element_name
        ]);
    }
}
