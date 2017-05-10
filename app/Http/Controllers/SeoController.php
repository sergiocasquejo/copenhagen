<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function meta($seoableType, $seoableId) {
        return response()->json(\App\Seo::where(['seoableId' => $seoableId, 'seoableType' => $seoableType])->first(), 200);
    }

    /**
	* Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id)
    {
        $seo = \App\Seo::find($id);
        
        $seo->rules['h1Tag'] = $seo->rules['h1Tag']  . ','. $seo->id . ',id';
        $seo->rules['slug'] = $seo->rules['slug']  . ','. $seo->id . ',id';

        $validator = $seo->validate($request->input(), $seo->rules);
        if ($validator->passes()) {
            
            $seo->metaTitle = $request->input('metaTitle');
            $seo->slug = str_slug($request->input('slug'));
            $seo->metaKeywords = $request->input('metaKeywords');
            $seo->metaDescription = $request->input('metaDescription');
            $seo->h1Tag = $request->input('h1Tag');
            $seo->redirect301 = $request->input('redirect301');
            $seo->canonicalLinks = $request->input('canonicalLinks');
            $seo->metaRobotTag = $request->input('metaRobotTag');
            $seo->metaRobotFollow = $request->input('metaRobotFollow');
            try{
                if ($seo->save()) {
                    
                    return response()->json('Successfully Saved.', 200, [], JSON_UNESCAPED_UNICODE);   
                }
            } catch(\Exception $e) {
                \Log::info('ERROR: '.$e->getMessage());
                return response()->json('Oops! Error please report to administrator.', 400, [], JSON_UNESCAPED_UNICODE);   
            }
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
