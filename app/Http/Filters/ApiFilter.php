<?php

namespace App\Http\Filters;

use Illuminate\Http\Request;

class ApiFilter{
    // parameter => operators
    protected $allowed = [];
    protected $columnMap = [];
    // query token to eloquent operator
    protected $operatorMap = [];

    public function transform(Request $request){
      $forEloquent = [];
      foreach($this->allowed as $param => $operators){
        $query = $request->query($param);
        if(!isset($query)) continue;

        $column = $this->columnMap[$param] ?? $param;

        foreach($operators as $operator){
            if(isset($query[$operator])){
                $op = $this->operatorMap[$operator];
                $val = $query[$operator];
                if(str_contains($op, 'like')){
                    /*$tmp = preg_replace('/^\'/', '', $query[$operator]);
                    $val = preg_replace('/\'$/', '', $tmp);*/
                    $val = '%'.addslashes($val).'%';
                }
                $forEloquent[] = [$column, $op, $val];
            }
        }
      }
      return $forEloquent;
    }
}