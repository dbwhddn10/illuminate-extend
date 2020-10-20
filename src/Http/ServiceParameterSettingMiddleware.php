<?php

namespace Illuminate\Extend\Http;

use Illuminate\Support\Arr;
use Illuminate\Extend\Service\Database\Trait\ModelTraitService;
use Illuminate\Extend\Service\Database\Trait\OrderByTraitService;
use Illuminate\Extend\Service\Database\Trait\ExpandsTraitService;
use Illuminate\Extend\Service\Database\Trait\FieldsTraitService;
use Illuminate\Extend\Service\Database\Trait\LimitTraitService;

class ServiceParameterSettingMiddleware
{
    public function handle($request, $next)
    {
        $response = $next($request);
        $content  = $response->getOriginalContent();
        $class    = $content[0];
        $data     = Arr::get($content, 1, []);
        $names    = Arr::get($content, 2, []);
        $traits   = $class::getAllTraits()->all();
        $loaders  = $class::getAllLoaders()->all();

        if ( $request->bearerToken() && ! $request->offsetExists('token') )
        {
            $data['token']  = $segs[1];
            $names['token'] = 'header[authorization]';
        }
        else if ( $request->offsetExists('token') )
        {
            $data['token']  = $request->offsetGet('token');
            $names['token'] = '[token]';
        }

        if ( in_array(ExpandsTraitService::class, $traits) )
        {
            $data['expands'] = Arr::get($request->all(), 'expands', '');
            $names['expands'] = '[expands]';
        }

        if ( in_array(FieldsTraitService::class, $traits) )
        {
            $data['fields'] = Arr::get($request->all(), 'fields', '');
            $names['fields'] = '[fields]';
        }

        if ( in_array(LimitTraitService::class, $traits) )
        {
            $data['limit'] = Arr::get($request->all(), 'limit', '');
            $names['limit'] = '[limit]';
        }

        if ( in_array(ModelTraitService::class, $traits) )
        {
            $data['id']  = $request->route('id');
            $names['id'] = $request->route('id');
        }

        if ( in_array(OrderByTraitService::class, $traits) )
        {
            $data['order_by'] = Arr::get($request->all(), 'order_by', '');
            $names['order_by'] = '[order_by]';
        }

        if ( array_key_exists('cursor', $loaders) )
        {
            $data['cursor_id']  = Arr::get($request->all(), 'cursor_id', '');
            $data['page']       = Arr::get($request->all(), 'page', '');
            $names['cursor_id'] = '[cursor_id]';
            $names['page']      = '[page]';
        }

        $response->setContent([$class, $data, $names]);

        return $response;
    }
}
