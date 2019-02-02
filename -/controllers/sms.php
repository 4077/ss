<?php namespace ss\controllers;

class Sms extends \Controller
{
    public function discard()
    {
        $v = $this->v('>discard');

        $this->css('>discard');

        if ($sms = \ss\models\OrderSms::where(\DB::raw('BINARY `discard_code`'), $this->data('code'))->first()) {
            if ($sms->sent) {
                $class = 'already_sent';
                $message = 'Смс-уведомление о заказе #' . $sms->order_id . ' уже отправлено';
            } else {
                if ($sms->discarded) {
                    $class = 'already_discarded';
                    $message = 'Смс-уведомление о заказе #' . $sms->order_id . ' уже отменено';
                } else {
                    $class = 'discarded';
                    $message = 'Смс-уведомление о заказе #' . $sms->order_id . ' отменено';

                    \ss\models\OrderSms::where(\DB::raw('BINARY `discard_code`'), $this->data('code'))
                        ->update([
                                     'discarded' => \Carbon\Carbon::now()->toDateTimeString()
                                 ]);
                }
            }
        } else {
            $class = 'wrong_code';
            $message = 'Неверный код';
        }

        $v->assign([
                       'CLASS'   => $class,
                       'MESSAGE' => $message
                   ]);

        return $v;
    }

    public function send()
    {
        $queue = \ss\models\OrderSms::where('discarded', null)
            ->where('sent', null)
            ->where('send_datetime', '<', \Carbon\Carbon::now())->get();

        foreach ($queue as $sms) {
            $this->c('\sms~:send', [
                'to'      => $sms->to,
                'message' => $sms->message
            ]);

            $sms->sent = \Carbon\Carbon::now()->toDateTimeString();
            $sms->save();
        }

        return count($queue);
    }
}
