<?php

namespace RoNoLo\Flydb\Format;

interface FormatInterface
{
    public function importFile($filePath);

    public function import($data);

    public function export();
}