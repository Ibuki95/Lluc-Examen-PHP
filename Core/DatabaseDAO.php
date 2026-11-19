<?php

namespace Core;

interface DatabaseDAO
{
    public function query($query, $params = []);
    public function get();
    public function find();
    public function findOrFail();
}