<?php

if (!function_exists("DtrecursiveMerge")) {
    /**
     * Funcion que permite la mezcla y extension de 2 matrices
     *
     * @param   array  $array1  Matriz uno
     * @param   array  $array2  Matriz 2
     *
     * @return  array
     */
    function DtrecursiveMerge(array $array1, array $array2): array
    {
        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($array1[$key]) && is_array($array1[$key])) {
                $array1[$key] = DtrecursiveMerge($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }
        return $array1;
    }
}