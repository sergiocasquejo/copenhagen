<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request, $id) {
        $user = \App\User::find($id);
        $rules = $user->rules;
        $rules['username'] = $rules['username']. ',' . $id . ',id';
        $rules['email'] = $rules['email']. ',' . $id . ',id';
        $validator = $user->validate($request->input(), $rules);
        if ($validator->passes()) {
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->password = bcrypt($request->input('password2'));
            $user->save();
            return response()->json('updated', 200, [], JSON_UNESCAPED_UNICODE);
        }

        $validationStr = '';
        foreach ($validator->errors()->getMessages() as $k => $error) {
            foreach ($error as $err) {
                $validationStr .= $err .'<br/>';
            }
            
        }

        return response()->json($validationStr, 400, [], JSON_UNESCAPED_UNICODE);
    }
}
