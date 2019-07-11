<?php
namespace tcc\V1\Rest\Checkin;

class CheckinResourceFactory
{
    public function __invoke($services)
    {
        return new CheckinResource();
    }
}
