<?php namespace ss\multisource\ui\division\importersControls\importerMap\controllers;

class Main extends \Controller
{
    private $importer;

    public function __create()
    {
        if ($this->importer = $this->unpackModel('importer')) {
            $this->instance_($this->importer->id);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $importer = $this->importer;
        $importerXPack = xpack_model($importer);

        $importMap = _j($importer->import_map) ?? [];

        $fields = handlers()->render('ss/multisource/ui/importer:import_map');

        foreach ($fields as $field => $label) {
            $v->assign('field', [
                'LABEL'      => $label,
                'EXPRESSION' => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:updateExpression',
                    'data'              => [
                        'field'    => $field,
                        'importer' => $importerXPack
                    ],
                    'class'             => 'txt',
                    'fitInputToClosest' => '.cell',
                    'selectOnFocus'     => true,
                    'content'           => $importMap[$field] ?? ''
                ]),
            ]);
        }

        $this->css();

        return $v;
    }
}
