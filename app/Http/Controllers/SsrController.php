<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\SerializableClosure\SerializableClosure;

class SsrController extends Controller
{
    public function __invoke(Request $request)
    {
        $page = json_decode(
            node([
                'url' => $request->getRequestUri(),
                'page' => $this->getPage($request),
            ])
        );

        return view('app', [
            'ssr' => [
                'head' => $page->head ?? '',
                'html' => $page->html ?? '',
                'state' => $page->state ?? '',
            ],
        ]);
    }

    protected function getPage(Request $request)
    {
        $route = Route::getRoutes()->match($request);
        
        if (! $route) {
            return null;
        }

        if ($route->isFallback) {
            return null;
        }

        $controller = $route->getController();
        
        if (! method_exists($controller, 'toArray')) {
            return null;
        }

        return $controller->toArray($request);
    }
}
