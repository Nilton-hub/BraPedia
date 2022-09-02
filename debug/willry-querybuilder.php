<?php
use Willry\QueryBuilder\DB;

require __DIR__ . "/../vendor/autoload.php";

// create
 $create = DB::table('users')
     ->create([
         "name" => "teste",
         "email" => "teste@mail.com"
     ]);

// read all
$data = DB::table('users')
    ->select()->get();
// var_dump($data);

// read one
$data = DB::table('users AS u')
    ->select()
    ->where("id = :i", ["i" => 2])
    ->get();
// var_dump($data);

//update
$update = DB::table('users')
    ->where('id = :num', [':num' => 3])
    ->update(['name' => 'Ana', 'email' => 'ana@mail.com']);
var_dump($update);

// delete
$delete = DB::table('users')
    ->where('id = :n', [':n' => 3])
    ->delete();
var_dump($delete);
