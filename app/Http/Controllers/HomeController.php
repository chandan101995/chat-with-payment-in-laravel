<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Models\Post;
use Auth;
use Carbon\Carbon;
use Session;
use Stripe;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // 
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function homePage()
    {
        return '<a href="/home">Go to Chat Page </a><br/><a href="/post">Post Upload</a><br/><a href="/check">Go to Payment</a>';
    }

    public function chatTheme()
    {
        $html = '';
        $users = User::with('messagesSeen')->where('id', '!=', Auth::user()->id)->get(['id', 'name', 'image', 'last_seen']);
        $id = Session::get('variableName');
        $data = User::with('messages')->where('id', $id)->first(['id', 'name', 'image', 'last_seen']);
        if ($data) {
            $a =  auth()->user()->messages()
                ->where(function ($query) use ($id) {
                    $query->bySender($id)
                        ->byReceiver(auth()->user()->id);
                })
                ->orWhere(function ($query) use ($id) {
                    $query->bySender(auth()->user()->id)
                        ->byReceiver($id);
                })
                ->get();
            $html = '<input type="hidden" name="r_id" value="' . $id . '"/>';
            $html .= '<h4>' . $data->name . '</h4>';
            if (count($a) > 0) {
                $html .= '<ul class="chat">';
                $html .= '<input type="hidden" name="r_id" value="' . $id . '"/>';
                foreach ($a as $chat) {
                    $uname = $chat->sender->id == auth()->user()->id ? $chat->sender->name : $data->name;
                    $uimage = $chat->sender->id == auth()->user()->id ? $chat->sender->image : $data->image;
                    $className = $chat->receiver_id == $id ? 'right' : 'left';
                    $html .= '<li class="' . $className . ' clearfix">';
                    $html .= '<span class="chat-img pull-' . $className . '">';
                    $html .= '<img src="' . $uimage . '" alt="' . $uname . '" title="' . $uname . '"/>';
                    $html .= '</span>';
                    $html .= '<div class="chat-body clearfix">';
                    $html .= '<div class="header">';
                    $html .= '<strong class="primary-font">' . $uname . '</strong>';
                    $html .= '<small class="pull-right text-muted"><i class="fa fa-clock-o"></i>' . \Carbon\Carbon::parse($chat->created_at)->diffForHumans() . '</small>';
                    $html .= '</div>';
                    $html .= '<p>' . $chat->message . '</p>';
                    $html .= '</div>';
                    $html .= '</li>';
                }
                $html .= '<div id="chatData"></div>';
                $html .= '</ul>';
            } else {
                $html .= '<p>No conversation. Please click on user to start a conversation</p>';
            }
        }
        return view('chat-theme', compact('users', 'html', 'data'));
    }

    public function userChat(Request $request)
    {
        $html = '';
        $id = request('u_id');
        // find your user by their email // optional - to ensure only one record is updated.
        Message::where('receiver_id', $id)->update(array('msg_status' => '1'));

        Session::put('variableName', $id);
        $data = User::with('messages')->where('id', $id)->first(['id', 'name', 'image', 'last_seen']);
        $a =  auth()->user()->messages()
            ->where(function ($query) {
                $query->bySender(request()->input('u_id'))
                    ->byReceiver(auth()->user()->id);
            })
            ->orWhere(function ($query) {
                $query->bySender(auth()->user()->id)
                    ->byReceiver(request()->input('u_id'));
            })
            ->get();
        $html = '<h4>' . $data->name . '</h4>';
        $html .= '<input type="hidden" name="r_id" value="' . $id . '"/>';
        $html .= '<ul class="chat">';
        if (count($a) > 0) {
            foreach ($a as $chat) {
                $uname = $chat->sender->id == auth()->user()->id ? $chat->sender->name : $data->name;
                $uimage = $chat->sender->id == auth()->user()->id ? $chat->sender->image : $data->image;
                $className = $chat->receiver_id == $id ? 'right' : 'left';
                $html .= '<li class="' . $className . ' clearfix">';
                $html .= '<span class="chat-img pull-' . $className . '">';
                $html .= '<img src="' . $uimage . '" alt="' . $uname . '" title="' . $uname . '"/>';
                $html .= '</span>';
                $html .= '<div class="chat-body clearfix">';
                $html .= '<div class="header">';
                $html .= '<strong class="primary-font">' . $uname . '</strong>';
                $html .= '<small class="pull-right text-muted"><i class="fa fa-clock-o"></i>' . \Carbon\Carbon::parse($chat->created_at)->diffForHumans() . '</small>';
                $html .= '</div>';
                $html .= '<p>' . $chat->message . '</p>';
                $html .= '</div>';
                $html .= '</li>';
            }
        }
        $html .= '<div id="chatData"></div>';
        $html .= '</ul>';
        return response()->json(['message' => 'Success', 'data' => $html]);
    }

    // chat message request
    public function chatMessage()
    {
        $r_id = request('r_id');
        $text_val = request('text_val');
        if ($r_id && $text_val) {
            $chat = new Message();
            $chat->sender_id = auth::user()->id;
            $chat->receiver_id = $r_id;
            $chat->message = $text_val;
            $chat->save();
        }
    }

    //post upload view 
    public function post()
    {
        return view('post');
    }

    //post upload index 
    public function index()
    {
        $posts = Post::with('media')->get();

        return view('index', compact('posts'));
    }

    //post upload index 
    public function postEdit($id)
    {
        $post = Post::with('media')->findOrFail($id);
        return view('edit', compact('post'));
    }

    // post store in database with media libreary
    public function postStore(Request $request)
    {
        $post = Post::create([
            'name' => $request->name
        ]);

        foreach ($request->input('document', []) as $file) {
            $post->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('document');
        }

        return redirect()->route('post.index');
    }

    // post upload 
    public function postUpload(Request $request)
    {
        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    // post update 
    public function postUpdate(Request $request, $id)
    {
        $post = Post::with('media')->findOrFail($id);
        $post->update($request->all());

        // if (count($post->document) > 0) {
        //     foreach ($post->document as $media) {
        //         if (!in_array($media->file_name, $request->input('document', []))) {
        //             $media->delete();
        //         }
        //     }
        // }

        // $media = $post->document->pluck('file_name')->toArray();

        // foreach ($request->input('document', []) as $file) {
        //     if (count($media) === 0 || !in_array($file, $media)) {
        //         $post->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('document');
        //     }
        // }
        return redirect()->route('post.index');
    }

    // check out page with stripe payment
    public function check(Request $request)
    {
        if ($request->isMethod('post')) {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $customer = \Stripe\Customer::create(array(
                'name' => 'Chandan Sharma',
                'description' => 'test description',
                'email' => 'chandan@gmail.com',
                'source' => $request->stripeToken,
                "address" => ["city" => "hyd", "country" => "india", "line1" => "510 Townsend St", "postal_code" => "98140", "state" => "telangana"]

            ));
            try {
                Stripe\Charge::create([
                    "amount" => 300 * 100,
                    "currency" => "usd",
                    "customer" =>  $customer["id"],
                    "description" => "Make payment and chill."
                ]);
                Session::flash('success-message', 'Payment done successfully !');
                return back();
            } catch (\Stripe\Error\Card $e) {
                Session::flash('fail-message', $e->get_message());
                return back();
            }
        }
        return view('check');
    }

    // check out handle function  with paypal 
    public function handlePayment()
    {
    }
}
