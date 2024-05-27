<?php
  namespace App\Helper;

  class NumberFormat
  {
    //It is used to replace the comma(,) in the string for numbers
    public static function string_replace($value){
      return str_replace(',', '', $value);
    }
  }