<?php namespace ss\Svc;

class Mailer extends \ewma\Service\Service
{
    protected $services = ['svc'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    //
    //
    //

    public function get($sender = false)
    {
        $mailer = $this->getDefaultMailer();

        $mailerCommonData = dataSets()->get('ss/svc/mailer:');

        if (!$sender) {
            $sender = $mailerCommonData['default_sender'];
        }

        $senderData = dataSets()->get('ss/svc/mailer:' . $sender);

        $mailer->isSMTP();
        $mailer->SMTPAuth = true;

        \ewma\Data\Data::extract($mailer, $senderData, '
            Host        host,
            Port        port,
            Username    user,
            Password    pass,
            SMTPSecure  smtp_secure,
            From        user,
            FromName    from_name
        ');

        $debug = dataSets()->get('ss/svc/mailer::debug');

        if ($debug) {
            $mailer->SMTPDebug = 2;
        }

        $appEnvId = app()->getEnv();

        if ($appEnvId != 'remote/prod') {
            $mailer->FromName .= ' (' . $appEnvId . ')';
        }

        $mailer->IsHTML();

        if ($bccRecipients = l2a($mailerCommonData['bcc_recipients'])) {
            foreach ($bccRecipients as $recipient) {
                $mailer->addBCC($recipient);
            }
        }

        return $mailer;
    }

    /**
     * @return \std\mailer\Mailer
     */
    private function getDefaultMailer()
    {
        return appc('\std\mailer~:get');
    }
}
