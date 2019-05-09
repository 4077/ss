<?php namespace ss\multisource;

class Svc
{
    public static $instance;

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new self;
        }

        return static::$instance;
    }

    public $mainController;

    public function __construct()
    {
        $this->mainController = appc('\ss\multisource\ui~');
    }

    //
    //
    //

    private $importersByEmails;

    public function getImportersByEmails()
    {
        if (null === $this->importersByEmails) {
            $divisions = \ss\multisource\models\Division::all();

            $this->importersByEmails = [];

            foreach ($divisions as $division) {
                $importers = table_rows_by_id($division->importers()->with('division')->where('enabled', true)->get());
                $workers = $division->workers;

                $emails = [];

                foreach ($workers as $worker) {
                    merge($emails, l2a($worker->emails));
                }

                foreach ($emails as $email) {
                    if (strpos($email, '@')) {
                        foreach ($importers as $importerId => $importer) {
                            $this->importersByEmails[$email][$importerId] = $importer;
                        }
                    }
                }
            }
        }

        return $this->importersByEmails;
    }

    public function getImportersByEmail($email)
    {
        $importersByEmails = $this->getImportersByEmails();

        return $importersByEmails[$email] ?? [];
    }
}
