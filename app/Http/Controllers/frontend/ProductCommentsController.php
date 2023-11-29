<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookCommentsRequest;
use App\Mail\BookCommentMail;
use App\Models\Accessor;
use App\Models\Books;
use Illuminate\Support\Facades\Mail;

class ProductCommentsController extends Controller
{

    /**
     * @param StoreBookCommentsRequest $request
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
     */
    public function store(StoreBookCommentsRequest $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            if (isset($request->product_type) && isset($request->product_id)) {
                match ($request->product_type) {
                    'book' => $productModel = new Books(),
                    'accessor' => $productModel = new Accessor(),
                };
                $createProductComment = $productModel::findOrfail($request->product_id)->comments()->create($request->all());
                Mail::to(env('EMAIL_NEW_MAG_CHILD'))->queue(new BookCommentMail($createProductComment));
                return redirect((url()->previous() . '#message-sent-successfully'))->with('send_successfully_message', __('messages.send_comment_message'));
            } else {
                return redirect((url()->previous() . '#message-sent-successfully'))->with('send_comment_wrong_message', __('messages.send_comment_message'));
            }
        } catch (\Exception $e) {
            return redirect((url()->previous() . '#message-sent-successfully'))->with('send_comment_wrong_message', __('messages.send_comment_message'));
        }
    }
}
