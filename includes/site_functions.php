<?php

function sortMultiArrayByKey($argArray, $argKey, $argOrder=SORT_DESC ){
        foreach ($argArray as $key => $row){
        $key_arr[$key] = $row[$argKey];
        }
        if($argArray){
            array_multisort($key_arr, $argOrder, $argArray);
        }
        return $argArray;
 }

?>