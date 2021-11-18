<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        // $data = BundleEquiptment::all();
        // return Inertia::render('BundleEquiptment', ['data' => $data]);
        
        $templates = EmailTemplate::all();
        return Inertia::render('Setup/EmailTemplate', ['templates' => $templates]);

    }
    public function store(Request $request){
        Validator::make($request->all(), [
            'name' => ['required'],
            'body' => ['required'],
        ])->validate();
        if($request->default){
           $type = ($request->type)?'email':'sms';
            EmailTemplate::where('type','=',$type)->update(['default'=>0]);
        }
        $template = new EmailTemplate();
        $template->name = $request->name;
        $template->title = $request->title;
        $template->body = $request->body;
        $template->cc_email = $request->cc_email;
        $template->type = ($request->type)?'email':'sms';
        $template->default = ($request->default)?1:0;
        $template->save();
        return redirect()->back()->with('message', 'Template Created Successfully.');
    }
    public function update(Request $request){
        Validator::make($request->all(), [
            'name' => ['required'],
            'body' => ['required'],
        ])->validate();
        if ($request->has('id')) {
            if($request->default){
                $type = ($request->type)?'email':'sms';
                 EmailTemplate::where('type','=',$type)->update(['default'=>0]);
             }
            $template =  EmailTemplate::find($request->input('id'));
            $template->name = $request->name;
            $template->title = $request->title;
            $template->body = $request->body;
            $template->cc_email = $request->cc_email;
            $template->type = ($request->type)?'email':'sms';
            $template->default = ($request->default)?1:0;
            $template->save();
        }
    
        return redirect()->back()->with('message', 'Template Updated Successfully.');
    }
}
