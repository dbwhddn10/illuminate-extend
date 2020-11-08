<?php

namespace Illuminate\Extend\Service\Database\Trait;

use Illuminate\Extend\Service;
use Illuminate\Extend\Service\Database\Trait\QueryTraitService;

class ModelTraitService extends Service
{
    public static function getArrBindNames()
    {
        return [];
    }

    public static function getArrCallbackLists()
    {
        return [
            'query.id' => ['id', 'query', function ($id, $query) {

                $query->where($query->getModel()->getKeyName(), $id);
            }],
        ];
    }

    public static function getArrLoaders()
    {
        return [
            'model' => ['query', function ($query) {

                return $query->first();
            }]
        ];
    }

    public static function getArrPromiseLists()
    {
        return [];
    }

    public static function getArrRuleLists()
    {
        return [
            'id'
                => ['required', 'integer'],

            'model'
                => ['not_null']
        ];
    }

    public static function getArrTraits()
    {
        return [
            QueryTraitService::class,
        ];
    }
}