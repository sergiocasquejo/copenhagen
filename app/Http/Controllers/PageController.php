<?php

namespace App\Http\Controllers;
use App\Mail\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class PageController extends Controller
{
    // Send Contact form
    public function contact(Request $request) {
        try {
            if (Mail::to(Config('mail.emails.reservation'))
            ->cc(Config('mail.emails.info'))
            ->send(new Contact($request->input()))) {
                return response()->json('Your message was sent successfully. Thanks.', 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json('Failed to send your message. Please try later or contact the administrator by another method.', 400, [], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Swift_TransportException  $e) {
            return response()->json('Failed to send your message. Please try later or contact the administrator by another method.', 400, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function index() {
        return $this->getAll();
    }

    public function getAll() {
        return \App\Page::all()->toArray();
    }
 
    public function store(Request $request) {
        try {
            $page = new \App\Page;
            $validator = $page->validate($request->input(), $page->rules);
            if ($validator->passes()) {
                
                $page->title = $request->input('title');
                $page->content = $request->input('content');
                $page->author = \Auth::user()->id;
                $page->status = $request->input('status', 0);
                if ($page->save()) {
                    $seo = new \App\Seo(['metaTitle' => $page->title, 'slug' => str_slug($page->title), 'h1Tag' => $page->title, 'seoableType' => 'page']);
                    $result = $page->seo()->save($seo);

                    return response()->json($page, 200, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return response()->json($validator->errors()->getMessages(), 400, [], JSON_UNESCAPED_UNICODE);
            }

        }catch(\Exception $e) {
            \Log::info('ERROR: '.$e->getMessage());
            return response()->json('Oops! Error please report to administrator.', 400, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function update(Request $request, $id) {
        try {
            $page = \App\Page::findOrFail($id);
            $validator = $page->validate($request->input(), $page->rules);
            if ($validator->passes()) {
                
                $page->title = $request->input('title');
                $page->content = $request->input('content');
                $page->status = $request->input('status', 0);
                if ($page->save()) {
                    return response()->json('Successfully saved.', 200, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return response()->json($validator->errors()->getMessages(), 400, [], JSON_UNESCAPED_UNICODE);
            }

        }catch(\Exception $e) {
            \Log::info('ERROR: '.$e->getMessage());
            return response()->json('Oops! Error please report to administrator.', 400, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function show($id) {
        $page = \App\Page::find($id);
        if ($page) {
            return $page->toArray();
        } else {
            return response()->json('Page not found', 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function destroy($id)
    {
        $result = \App\Page::find($id)->delete();
        return response()->json($this->getAll(), 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function showBySlug(Request $request, $slug) {
        $seo = \App\Seo::where(['slug' => $slug, 'seoableType' => 'page'])->first();
        if ($seo) {
            $room = \App\Page::where(['id' => $seo->seoableId, 'status' => 1])->first();
            if ($room) {
                return response()->json($room->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
            } 
        }
        
        return response()->json("Sorry, Page not found.", 400, [], JSON_UNESCAPED_UNICODE);
    }
}
