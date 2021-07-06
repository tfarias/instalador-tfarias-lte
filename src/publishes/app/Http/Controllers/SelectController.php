<?php

namespace App\Http\Controllers;


use Illuminate\Support\Str;

class SelectController extends Controller
{
    /**
     * Filtra um registro para os campos select2.
     *
     * @param
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fill()
    {
        $request = request()->all();
        $termo = $request['termo'];
        $size = $request['size'];
        $page = (!isset($request['page']) || $request['page'] < 1) ? 1 : $request['page'];

        if (!isset($termo))
            $termo = '';
        if (!isset($size) || $size < 1)
            $size = 10;

        $model = request()->get('model');
        $model = ucfirst(Str::camel($model));
        $instancia = '\App\Models\\' . $model;
        $campo = request()->get('campo');
        $registros = $instancia::where($campo, 'like','%' . $termo . '%')->orderBy($campo,"ASC");

        $count = $registros->count();
        $ret["more"] = (($size * ($page - 1)) >= (int)$count) ? false : true;
        $ret["total"] = $count;
        $ret["dados"] = array();
        $registros->limit($size);
        $registros->offset($size * ($page - 1));
        $result = $registros->get();
        foreach ($result as $d) {
            $ret["dados"][] = array('id' => $d->id, 'text' => $d->$campo);
        }
        return response()->json($ret);
    }

    /**
     *  Filtra um registro para os campos select2.
     *
     * @param $id
     */
    public function getEdit($id)
    {
        $model = request()->get('model');
        $model = ucfirst(Str::camel($model));
        $campo = request()->get('campo');
        $instancia = '\App\Models\\' . $model;
        $result = $instancia::find($id);
        $res = ['nome'=>'selecione','id'=>null];
        if(!empty($result)){
            $res = ['nome'=>$result->$campo,'id'=>$result->id];
        }
        return response()->json($res);
    }

}
