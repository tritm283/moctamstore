<?php
namespace App\Http\Controllers;
abstract class Controller
{
    protected function ok(mixed $data=null, string $message='OK', int $status=200) { return response()->json(['message'=>$message,'data'=>$data],$status); }
}
