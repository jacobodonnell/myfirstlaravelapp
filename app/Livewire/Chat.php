<?php

namespace App\Livewire;

use App\Events\ChatMessage;
use Livewire\Component;

class Chat extends Component {

    public $textvalue = '';
    public $chatLog = [];

    public function getListeners() {
        return [
            "echo-private:chatchannel,ChatMessage" => 'notifyNewMessage'
        ];
    }

    public function notifyNewMessage($message) {
        array_push($this->chatLog, $message['chat']);
    }

    public function send() {
        /** @var StatefulGuard $auth */
        $auth = auth();
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        if (trim(strip_tags($this->textvalue)) == "") {
            return;
        }

        $message = ['selfmessage' => true, 'username' => $auth->user()->username, 'textvalue' => strip_tags($this->textvalue), 'avatar' => $auth->user()->avatar];
        array_push($this->chatLog, $message);

        $message['selfmessage'] = false;
        broadcast(new ChatMessage($message))->toOthers();

        $this->textvalue = '';
    }

    public function render() {
        return view('livewire.chat');
    }
}
