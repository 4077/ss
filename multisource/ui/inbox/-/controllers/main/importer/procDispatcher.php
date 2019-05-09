<?php namespace ss\multisource\ui\inbox\controllers\main\importer;

class ProcDispatcher extends \Controller
{
    public function view()
    {
        $runningAIPivots = \ss\multisource\models\InboxAttachmentImporter::where('import_proc_pid', '!=', false)->get();

        $importXPids = [];
        foreach ($runningAIPivots as $pivot) {
            if ($process = $this->app->processDispatcher->open($pivot->import_proc_pid)) {
                $importXPids[] = [
                    'aiPivotId' => $pivot->id,
                    'xpid'      => $process->getXPid()
                ];
            } else {
                $pivot->import_proc_pid = false;
                $pivot->save();
            }
        }

        $this->widget(':', [
            'importerSelector' => $this->_selector('<:'),
            'importXPids'      => $importXPids
        ]);

        return $this->v();
    }
}
