<?php namespace ss\Svc\Search;

class Query extends \ewma\Service\Service
{
    protected $services = ['svc'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    //
    //
    //

    private $garbageWords = [];

    private $wordsEndings = [];

    public function boot()
    {
        $this->garbageWords = l2a('на, для, от, к, из, в');
        $this->wordsEndings = implode('|', l2a('ованный, янный, овый, овая, овое, овой, овые, нный, ый, ой, ий, ая, ые, ое, ие, ы, и, а, я, у, ю, ь'));
    }

    //
    //
    //

    public function cleanQuery($input)
    {
        $output = trim($input);
        $output = mb_strtolower($output);
        $output = mb_ereg_replace('/[^A-Za-zА-Яа-я0-9\.\,]/', ' ', $output);
        $output = str_replace(',', '.', $output);
        $output = str_replace(['"', '\''], '', $output);

        return $output;
    }

    private function trimEnding($word)
    {
        return preg_replace('/(' . $this->wordsEndings . ')$/', '', $word);
    }

    public function getIndex($input)
    {
        $clearQuery = $this->cleanQuery($input);

        $words = explode(' ', $clearQuery);

        $outputWords = [];
        foreach ($words as $word) {
            if ($word && !in($word, $this->garbageWords)) {
                $outputWords[] = $this->trimEnding($word);
            }
        }

        return ' ' . implode(' ', merge($words, $outputWords, true)) . ' ';
    }

    public function getFullWords($input)
    {
        $clearQuery = $this->cleanQuery($input);

        $words = explode(' ', $clearQuery);

        $output = [];

        foreach ($words as $word) {
            if ($word && !in($word, $this->garbageWords)) {
                $output[] = $word;
            }
        }

        return $output;
    }

    public function getTrimmedWords($input)
    {
        $clearQuery = $this->cleanQuery($input);

        $words = explode(' ', $clearQuery);

        $output = [];

        foreach ($words as $word) {
            if ($word && !in($word, $this->garbageWords)) {
                $output[] = $this->trimEnding($word);
            }
        }

        return $output;
    }
}
