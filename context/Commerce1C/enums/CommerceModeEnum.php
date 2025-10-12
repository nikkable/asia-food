<?php

namespace context\Commerce1C\enums;

enum CommerceModeEnum: string
{
    case CHECKAUTH = 'checkauth';
    case INIT = 'init';
    case FILE = 'file';
    case IMPORT = 'import';
    case SUCCESS = 'success';
    case QUERY = 'query';
}
