<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\View\JsonView;

class MidniteController extends AppController
{

    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    public function index()
    {
        $this->request->allowMethod(['post']);

        $data = $this->request->getData();
        dd($data);
    }
}