<?php
declare(strict_types=1);

function logs($value): void
{
   if (!is_scalar($value) || is_array($value)) {
       $value = json_encode($value);
   }
    error_log($value);
}
