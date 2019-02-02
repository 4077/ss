<?php namespace ss\controllers;

class Test extends \Controller
{
    public function irkutskMarazzi()
    {
        $keramaProducts = table_rows_by(\ss\models\Product::where('tree_id', 21)->get(), 'remote_articul');
        $marazziProducts = table_rows_by(\ss\models\Product::where('tree_id', 34)->get(), 'vendor_code');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $worksheet = $spreadsheet->getActiveSheet();

        $output = [];

        $styleRed = [
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF5522',
                ],
                'endColor'   => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];

        $n = 1;
        $nAll = 1;

        $worksheet->getCell('A' . $n)->setValue('Артикул');
        $worksheet->getCell('B' . $n)->setValue('Остаток');
        $worksheet->getCell('C' . $n)->setValue('Иркутск');
        $worksheet->getCell('D' . $n)->setValue('MARAZZI');

        foreach ($keramaProducts as $vendorCode => $product) {
            $nAll++;

            $multisourceCache = _j($product->multisource_cache);

            if (($multisourceCache[6]['total_stock'] ?? 0) > 0) {
                $n++;
            }

            $stock = $multisourceCache[6]['total_stock'] ?? 0;

            $worksheet->getCell('A' . $n)->setValue($vendorCode);
            $worksheet->getCell('B' . $n)->setValue($stock);
            $worksheet->getCell('C' . $n)->setValue($product->name);
            $worksheet->getCell('D' . $n)->setValue($marazziProducts[$vendorCode]->name ?? '----');

            if ($stock == 0) {
                $worksheet->getStyle('A' . $n . ':D' . $n)->applyFromArray($styleRed);
            }


            $output[$vendorCode] = [
                'irkutsk' => $product->name,
                'marazzi' => $marazziProducts[$vendorCode]->name ?? '----'
            ];
//            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($this->_protected('irkutsk_vs_marazzi.xlsx'));

        return ['n' => $n, 'nAll' => $nAll];
    }

    public function mailtest()
    {
        $mailer = mailer('mailers:dev');

        $mailer->Subject = 'mailtest';
        $mailer->Body = dt();

        $mailer->queue('mail');
    }

    public function j64CallA()
    {
        return j64_($this->_abs(':write:A,6', $this->data));
    }

    public function j64CallB()
    {
        return j64_($this->_abs(':write:B,2', [
            'x' => [
                'y' => 55,
                'z' => [2, 4, 5]
            ]
        ]));
    }

//    public function async($path, $data = [])
//    {
//        $command = 'nohup ./cli ' . $path . ' ' . a2s($data) . ' > async.log &';
//
//        $cwd = getcwd();
//
//        chdir(app()->root);
//        exec($command, $output);
//        chdir($cwd);
//
//        return $output;
//
////        $process = new \Symfony\Component\Process\Process($command, app()->root);
////
////        $process->start();
////
////        return $process;
//    }

    public function crontestA()
    {
//        appc('\std\queue~:add', [
//            'ttl'   => 300,
//            'call'  => $this->_abs(':write:A,6', $this->data),
//            'async' => true
//        ]);

//        $process = $this->async('-n ss test:write:A,4');
//
//        return $process;

        $this->write('A', 5);
    }

    public function crontestB()
    {
//        $process = $this->async('-n ss test:write:B,2', $this->data);

//        appc('\std\queue~:add', [
//            'ttl'   => 300,
//            'call'  => $this->_abs(':write:B,2', $this->data),
//            'async' => true
//        ]);

        $this->write('B', 2);

//        return $process;
    }

    public function write($name, $sleep = 0)
    {
        sleep($sleep);

        mdir($this->_protected('crontest'));

        $file = fopen($this->_protected('crontest/' . $name), 'a');
        fwrite($file, dt() . ' -> ' . a2s($this->data) . PHP_EOL);
        fclose($file);

        return dt();
    }

    public function otherSession()
    {
        $key = $this->data('key');
        $instance = $this->data('instance');

        $s = $this->otherS($key, '\ss\cart~:|' . $instance);

        return $s;
    }

    public function lemmatize()
    {
        $morphy = new \cijic\phpMorphy\Morphy('ru');

        $r = $morphy->getBaseForm('дверях');

        return $r;
    }

    public function phpmorphy()
    {
        $products = \ss\models\Product::where('tree_id', 2)->offset(rand(1, 3000))->take(1)->get();

//        $phpmorphy = new \phpmorphy\PhpMorphy;

        $morphy = new \cijic\phpMorphy\Morphy('ru');

        foreach ($products as $product) {
            $name = ss()->search->query->cleanQuery($product->name);

            print '==============================' . PHP_EOL;
            print $name . PHP_EOL;

            $words = ss()->search->query->getFullWords($name);

            foreach ($words as $word) {
                $pseudoRoot = $morphy->lemmatize($word);

                p([$word, $pseudoRoot]);
            }

            print '==============================' . PHP_EOL;
            print PHP_EOL;
        }
    }

    public function bilet()
    {
        return rand(27, 30);
    }

    public function multisource()
    {
//        $product = \ss\models\Product::find(422650);

//        ss()->products->updateMultisourceData($product);


//        $tree = \ss\models\Tree::find(4);
//
//        ss()->trees->updateMultisourceData($tree, $this->data('sleep'));
//        ss()->trees->updateMultisourceData($tree, $product);
    }

    public function ping3()
    {
        $client = new \GuzzleHttp\Client();

        $onRedirect = function (
            $request,
            $response,
            $uri
        ) {
            echo 'Redirecting! ' . $request->getUri() . ' to ' . $uri . "\n";
        };

//        $response = $client->request('GET', 'https://www.marathonbet.com/su/popular/Tennis/ITF/?menu=370467', [
//            'headers'         => [
//                'Accept'                    => 'text/html,application/xhtml+xm…plication/xml;q=0.9,*/*;q=0.8',
//                'Accept-Encoding'           => 'gzip, deflate, br',
//                'Accept-Language'           => 'en-US,en;q=0.5',
//                'Cache-Control'             => 'max-age=0',
//                'Connection'                => 'keep-alive',
//                'Cookie'                    => '_dvs_old=0%3Ajmnmwa00%3AOyk_wfpt9_al~jRG37jrvB84oPRhLYOG; __cfduid=dc8492e853a7b6f5d66e32065ff378e401532728883; panbet.openeventnameseparately=true; panbet.openadditionalmarketsseparately=false; puid=rBkp81tbljODrRsAEBF8Ag==; last_visit=1538262735041::1538237535041; LIVE_TRENDS_STYLE=TEXT_COLOR; SyncTimeData={"offset":56,"timestamp":1538241737005}; _ym_uid=153272888740335566; _ym_d=1532728887; _ga=GA1.2.618078777.1532728887; _dvp=0:jk4j7e2g:RxVR6hKJZqIAVg75JFylFOFkjaygy0eB; __utma=165002403.618078777.1532728887.1537710525.1538237510.294; __utmz=165002403.1532728888.1.1.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided); _tok=546e1682f6416bce9866cb1f0a6e5ee3|3b88002a0e0136380293a9d5f56e7745|e40509ed9119faf7592d2c39b29acd5d; panbet.oddstype=Decimal; fingerprint=88c3238f77db2c9a9f2128ddcceac75d; SESSION_KEY=e3089b3522f546fa9af74af9171bc616; _gid=GA1.2.1920431645.1538237509; _ym_isad=2; __utmc=165002403; bonusMark=3298762; PUNTER_KEY=ME5N2A7SPM8875V6N5IUGB17J8PAFFFT; PUNTER_KEY_EXISTS=yes; MJSESSIONID=web8~B1CDD40F8A2B11E1ED7172A5A9160A66; JSESSIONID=web3~FC65FA1B409E7EAF9832593631725F4C',
//                'Host'                      => 'www.marathonbet.com',
//                'Referer'                   => 'https://www.marathonbet.com/su/',
//                'TE'                        => 'Trailers',
//                'Upgrade-Insecure-Requests' => '1',
//                'User-Agent'                => 'Mozilla/5.0 (Windows NT 6.3; W…) Gecko/20100101 Firefox/62.0'
//            ],
//            'proxy'           => 'usr12091626:ps120911538246626@217.147.171.44:4040',
//            'allow_redirects' => [
//                'max'             =>20,        // allow at most 10 redirects.
//                'strict'          => true,      // use "strict" RFC compliant redirects.
//                'referer'         => true,      // add a Referer header
////                'protocols'       => ['https'], // only allow https URLs
//                'on_redirect'     => $onRedirect,
//                'track_redirects' => true
//            ]
//        ]);


        $response = $client->request('POST', 'https://www.marathonbet.com/su/betslip/placebet2.htm', [
            'headers'         => [
                'Accept'                    => 'text/html,application/xhtml+xm…plication/xml;q=0.9,*/*;q=0.8',
                'Accept-Encoding'           => 'gzip, deflate, br',
                'Accept-Language'           => 'en-US,en;q=0.5',
                'Cache-Control'             => 'max-age=0',
                'Connection'                => 'keep-alive',
                'Cookie'                    => '_dvs_old=0%3Ajmnpfszp%3ApTNxgJTXDcTvL6XBzy832~Fs33X9ZRoh; _dvs_old=0%3Ajmnmwa00%3AOyk_wfpt9_al~jRG37jrvB84oPRhLYOG; __cfduid=dc8492e853a7b6f5d66e32065ff378e401532728883; panbet.openeventnameseparately=true; panbet.openadditionalmarketsseparately=false; puid=rBkp81tbljODrRsAEBF8Ag==; last_visit=1538274616825::1538249416825; LIVE_TRENDS_STYLE=TEXT_COLOR; SyncTimeData={"offset":-12,"timestamp":1538249417706}; _ym_uid=153272888740335566; _ym_d=1532728887; _ga=GA1.2.618078777.1532728887; _dvp=0:jk4j7e2g:RxVR6hKJZqIAVg75JFylFOFkjaygy0eB; __utma=165002403.618078777.1532728887.1538237510.1538241781.295; __utmz=165002403.1532728888.1.1.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided); _tok=546e1682f6416bce9866cb1f0a6e5ee3|3b88002a0e0136380293a9d5f56e7745|e40509ed9119faf7592d2c39b29acd5d; panbet.oddstype=Decimal; fingerprint=88c3238f77db2c9a9f2128ddcceac75d; SESSION_KEY=04b143d0f40d4ddfb63b0e58bf9a28c9; _gid=GA1.2.1920431645.1538237509; _ym_isad=2; __utmc=165002403; PUNTER_KEY=6HAIGKLAMQL1Q8TVBSUL6I030TL31B99; PUNTER_KEY_EXISTS=yes; _dvs=0:jmnpfszp:pTNxgJTXDcTvL6XBzy832~Fs33X9ZRoh; MJSESSIONID=web5~214EACE960A9EF459C0F5E625F04D670; JSESSIONID=web5~2008E3CF7585FB3B6F3EB8B73B6B13BF; bonusMark=3298762; MWSESSIONID=DF76B4595188E1EBBB247B4ABBCB895C; _dc_gtm_UA-55273062-1=1; _dc_gtm_UA-55273062-3=1; _gat_UA-55273062-18=1; _ym_visorc_24133222=b',
                'Host'                      => 'www.marathonbet.com',
                'Referer'                   => 'https://www.marathonbet.com/su/',
                'TE'                        => 'Trailers',
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent'                => 'Mozilla/5.0 (Windows NT 6.3; W…) Gecko/20100101 Firefox/62.0'
            ],
            'proxy'           => 'usr12091626:ps120911538246626@217.147.171.44:4040',
            'form_params'     => [
                'p'       => 'SINGLES',
                'b'       => ['url' => 6504397, 'Total_Games0.Under_20.5', 'stake' => 0.3, 'vip' => false, 'ew' => false],
                'choices' => ['selectionUid' => 6504397, 'Total_Games0.Under_20.5', 'cfId' => '25530667915', 'eprice' => 1.9]
            ],
            'allow_redirects' => [
                'max'             => 20,        // allow at most 10 redirects.
                'strict'          => true,      // use "strict" RFC compliant redirects.
                'referer'         => true,      // add a Referer header
                //                'protocols'       => ['https'], // only allow https URLs
                'on_redirect'     => $onRedirect,
                'track_redirects' => true
            ]
        ]);

        $c = $response->getBody()->getContents();

        p($c);
    }

    public function ping2()
    {
        $client = new \GuzzleHttp\Client();

        $onRedirect = function (
            $request,
            $response,
            $uri
        ) {
            echo 'Redirecting! ' . $request->getUri() . ' to ' . $uri . "\n";
        };

//        $response = $client->request('GET', 'https://www.marathonbet.com/su/popular/Tennis/ITF/?menu=370467', [
//            'headers'         => [
//                'Accept'                    => 'text/html,application/xhtml+xm…plication/xml;q=0.9,*/*;q=0.8',
//                'Accept-Encoding'           => 'gzip, deflate, br',
//                'Accept-Language'           => 'en-US,en;q=0.5',
//                'Cache-Control'             => 'max-age=0',
//                'Connection'                => 'keep-alive',
//                'Cookie'                    => '_dvs_old=0%3Ajmnmwa00%3AOyk_wfpt9_al~jRG37jrvB84oPRhLYOG; __cfduid=dc8492e853a7b6f5d66e32065ff378e401532728883; panbet.openeventnameseparately=true; panbet.openadditionalmarketsseparately=false; puid=rBkp81tbljODrRsAEBF8Ag==; last_visit=1538262735041::1538237535041; LIVE_TRENDS_STYLE=TEXT_COLOR; SyncTimeData={"offset":56,"timestamp":1538241737005}; _ym_uid=153272888740335566; _ym_d=1532728887; _ga=GA1.2.618078777.1532728887; _dvp=0:jk4j7e2g:RxVR6hKJZqIAVg75JFylFOFkjaygy0eB; __utma=165002403.618078777.1532728887.1537710525.1538237510.294; __utmz=165002403.1532728888.1.1.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided); _tok=546e1682f6416bce9866cb1f0a6e5ee3|3b88002a0e0136380293a9d5f56e7745|e40509ed9119faf7592d2c39b29acd5d; panbet.oddstype=Decimal; fingerprint=88c3238f77db2c9a9f2128ddcceac75d; SESSION_KEY=e3089b3522f546fa9af74af9171bc616; _gid=GA1.2.1920431645.1538237509; _ym_isad=2; __utmc=165002403; bonusMark=3298762; PUNTER_KEY=ME5N2A7SPM8875V6N5IUGB17J8PAFFFT; PUNTER_KEY_EXISTS=yes; MJSESSIONID=web8~B1CDD40F8A2B11E1ED7172A5A9160A66; JSESSIONID=web3~FC65FA1B409E7EAF9832593631725F4C',
//                'Host'                      => 'www.marathonbet.com',
//                'Referer'                   => 'https://www.marathonbet.com/su/',
//                'TE'                        => 'Trailers',
//                'Upgrade-Insecure-Requests' => '1',
//                'User-Agent'                => 'Mozilla/5.0 (Windows NT 6.3; W…) Gecko/20100101 Firefox/62.0'
//            ],
//            'proxy'           => 'usr12091626:ps120911538246626@217.147.171.44:4040',
//            'allow_redirects' => [
//                'max'             =>20,        // allow at most 10 redirects.
//                'strict'          => true,      // use "strict" RFC compliant redirects.
//                'referer'         => true,      // add a Referer header
////                'protocols'       => ['https'], // only allow https URLs
//                'on_redirect'     => $onRedirect,
//                'track_redirects' => true
//            ]
//        ]);


        $response = $client->request('GET', 'https://www.marathonbet.com/', [
            'headers'         => [
                'Accept'                    => 'text/html,application/xhtml+xm…plication/xml;q=0.9,*/*;q=0.8',
                'Accept-Encoding'           => 'gzip, deflate, br',
                'Accept-Language'           => 'en-US,en;q=0.5',
                'Cache-Control'             => 'max-age=0',
                'Connection'                => 'keep-alive',
                'Cookie'                    => '_dvs_old=0%3Ajmnpfszp%3ApTNxgJTXDcTvL6XBzy832~Fs33X9ZRoh; _dvs_old=0%3Ajmnmwa00%3AOyk_wfpt9_al~jRG37jrvB84oPRhLYOG; __cfduid=dc8492e853a7b6f5d66e32065ff378e401532728883; panbet.openeventnameseparately=true; panbet.openadditionalmarketsseparately=false; puid=rBkp81tbljODrRsAEBF8Ag==; last_visit=1538272504566::1538247304566; LIVE_TRENDS_STYLE=TEXT_COLOR; SyncTimeData={"offset":13,"timestamp":1538247305397}; _ym_uid=153272888740335566; _ym_d=1532728887; _ga=GA1.2.618078777.1532728887; _dvp=0:jk4j7e2g:RxVR6hKJZqIAVg75JFylFOFkjaygy0eB; __utma=165002403.618078777.1532728887.1538237510.1538241781.295; __utmz=165002403.1532728888.1.1.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided); _tok=546e1682f6416bce9866cb1f0a6e5ee3|3b88002a0e0136380293a9d5f56e7745|e40509ed9119faf7592d2c39b29acd5d; panbet.oddstype=Decimal; fingerprint=88c3238f77db2c9a9f2128ddcceac75d; SESSION_KEY=04b143d0f40d4ddfb63b0e58bf9a28c9; _gid=GA1.2.1920431645.1538237509; _ym_isad=2; __utmc=165002403; bonusMark=3298762; MJSESSIONID=web6~AAD81FFCEA2F4F821B36D78DCACBBA29; JSESSIONID=web5~E6DAB7D071D086B66362381610062865; PUNTER_KEY=6HAIGKLAMQL1Q8TVBSUL6I030TL31B99; PUNTER_KEY_EXISTS=yes; _dvs=0:jmnpfszp:pTNxgJTXDcTvL6XBzy832~Fs33X9ZRoh; MWSESSIONID=26C87CC2EA03664DF34C82A9FDC95A93; _dc_gtm_UA-55273062-1=1; _dc_gtm_UA-55273062-3=1; _gat_UA-55273062-18=1; _ym_visorc_24133222=w',
                'Host'                      => 'www.marathonbet.com',
                'Referer'                   => 'https://www.marathonbet.com/su/',
                'TE'                        => 'Trailers',
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent'                => 'Mozilla/5.0 (Windows NT 6.3; W…) Gecko/20100101 Firefox/62.0'
            ],
            'proxy'           => 'usr12091626:ps120911538246626@217.147.171.44:4040',
            'allow_redirects' => [
                'max'             => 20,        // allow at most 10 redirects.
                'strict'          => true,      // use "strict" RFC compliant redirects.
                'referer'         => true,      // add a Referer header
                //                'protocols'       => ['https'], // only allow https URLs
                'on_redirect'     => $onRedirect,
                'track_redirects' => true
            ]
        ]);

        $c = $response->getBody()->getContents();

        p($c);
    }

    public function ping4()
    {
        $client = new \GuzzleHttp\Client();

        $onRedirect = function (
            $request,
            $response,
            $uri
        ) {
            echo 'Redirecting! ' . $request->getUri() . ' to ' . $uri . "\n";
        };

//        $response = $client->request('GET', 'https://www.marathonbet.com/su/popular/Tennis/ITF/?menu=370467', [
//            'headers'         => [
//                'Accept'                    => 'text/html,application/xhtml+xm…plication/xml;q=0.9,*/*;q=0.8',
//                'Accept-Encoding'           => 'gzip, deflate, br',
//                'Accept-Language'           => 'en-US,en;q=0.5',
//                'Cache-Control'             => 'max-age=0',
//                'Connection'                => 'keep-alive',
//                'Cookie'                    => '_dvs_old=0%3Ajmnmwa00%3AOyk_wfpt9_al~jRG37jrvB84oPRhLYOG; __cfduid=dc8492e853a7b6f5d66e32065ff378e401532728883; panbet.openeventnameseparately=true; panbet.openadditionalmarketsseparately=false; puid=rBkp81tbljODrRsAEBF8Ag==; last_visit=1538262735041::1538237535041; LIVE_TRENDS_STYLE=TEXT_COLOR; SyncTimeData={"offset":56,"timestamp":1538241737005}; _ym_uid=153272888740335566; _ym_d=1532728887; _ga=GA1.2.618078777.1532728887; _dvp=0:jk4j7e2g:RxVR6hKJZqIAVg75JFylFOFkjaygy0eB; __utma=165002403.618078777.1532728887.1537710525.1538237510.294; __utmz=165002403.1532728888.1.1.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided); _tok=546e1682f6416bce9866cb1f0a6e5ee3|3b88002a0e0136380293a9d5f56e7745|e40509ed9119faf7592d2c39b29acd5d; panbet.oddstype=Decimal; fingerprint=88c3238f77db2c9a9f2128ddcceac75d; SESSION_KEY=e3089b3522f546fa9af74af9171bc616; _gid=GA1.2.1920431645.1538237509; _ym_isad=2; __utmc=165002403; bonusMark=3298762; PUNTER_KEY=ME5N2A7SPM8875V6N5IUGB17J8PAFFFT; PUNTER_KEY_EXISTS=yes; MJSESSIONID=web8~B1CDD40F8A2B11E1ED7172A5A9160A66; JSESSIONID=web3~FC65FA1B409E7EAF9832593631725F4C',
//                'Host'                      => 'www.marathonbet.com',
//                'Referer'                   => 'https://www.marathonbet.com/su/',
//                'TE'                        => 'Trailers',
//                'Upgrade-Insecure-Requests' => '1',
//                'User-Agent'                => 'Mozilla/5.0 (Windows NT 6.3; W…) Gecko/20100101 Firefox/62.0'
//            ],
//            'proxy'           => 'usr12091626:ps120911538246626@217.147.171.44:4040',
//            'allow_redirects' => [
//                'max'             =>20,        // allow at most 10 redirects.
//                'strict'          => true,      // use "strict" RFC compliant redirects.
//                'referer'         => true,      // add a Referer header
////                'protocols'       => ['https'], // only allow https URLs
//                'on_redirect'     => $onRedirect,
//                'track_redirects' => true
//            ]
//        ]);


        $response = $client->request('GET', 'https://www.marathonbet.com/cdn/3-0-655-950/js/common/panbet.js', [
            'headers'         => [
                'Accept'                    => 'text/html,application/xhtml+xm…plication/xml;q=0.9,*/*;q=0.8',
                'Accept-Encoding'           => 'gzip, deflate, br',
                'Accept-Language'           => 'en-US,en;q=0.5',
                'Cache-Control'             => 'max-age=0',
                'Connection'                => 'keep-alive',
                //                'Cookie'                    => '_dvs_old=0%3Ajmnpfszp%3ApTNxgJTXDcTvL6XBzy832~Fs33X9ZRoh; _dvs_old=0%3Ajmnmwa00%3AOyk_wfpt9_al~jRG37jrvB84oPRhLYOG; __cfduid=dc8492e853a7b6f5d66e32065ff378e401532728883; panbet.openeventnameseparately=true; panbet.openadditionalmarketsseparately=false; puid=rBkp81tbljODrRsAEBF8Ag==; last_visit=1538272504566::1538247304566; LIVE_TRENDS_STYLE=TEXT_COLOR; SyncTimeData={"offset":13,"timestamp":1538247305397}; _ym_uid=153272888740335566; _ym_d=1532728887; _ga=GA1.2.618078777.1532728887; _dvp=0:jk4j7e2g:RxVR6hKJZqIAVg75JFylFOFkjaygy0eB; __utma=165002403.618078777.1532728887.1538237510.1538241781.295; __utmz=165002403.1532728888.1.1.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided); _tok=546e1682f6416bce9866cb1f0a6e5ee3|3b88002a0e0136380293a9d5f56e7745|e40509ed9119faf7592d2c39b29acd5d; panbet.oddstype=Decimal; fingerprint=88c3238f77db2c9a9f2128ddcceac75d; SESSION_KEY=04b143d0f40d4ddfb63b0e58bf9a28c9; _gid=GA1.2.1920431645.1538237509; _ym_isad=2; __utmc=165002403; bonusMark=3298762; MJSESSIONID=web6~AAD81FFCEA2F4F821B36D78DCACBBA29; JSESSIONID=web5~E6DAB7D071D086B66362381610062865; PUNTER_KEY=6HAIGKLAMQL1Q8TVBSUL6I030TL31B99; PUNTER_KEY_EXISTS=yes; _dvs=0:jmnpfszp:pTNxgJTXDcTvL6XBzy832~Fs33X9ZRoh; MWSESSIONID=26C87CC2EA03664DF34C82A9FDC95A93; _dc_gtm_UA-55273062-1=1; _dc_gtm_UA-55273062-3=1; _gat_UA-55273062-18=1; _ym_visorc_24133222=w',
                'Host'                      => 'www.marathonbet.com',
                'Referer'                   => 'https://www.marathonbet.com/su/',
                'TE'                        => 'Trailers',
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent'                => 'Mozilla/5.0 (Windows NT 6.3; W…) Gecko/20100101 Firefox/62.0'
            ],
            'proxy'           => 'usr12091626:ps120911538246626@217.147.171.44:4040',
            'allow_redirects' => [
                'max'             => 20,        // allow at most 10 redirects.
                'strict'          => true,      // use "strict" RFC compliant redirects.
                'referer'         => true,      // add a Referer header
                //                'protocols'       => ['https'], // only allow https URLs
                'on_redirect'     => $onRedirect,
                'track_redirects' => true
            ]
        ]);

        $c = $response->getBody()->getContents();

        p($c);
    }

    public function ping()
    {
        $client = new \GuzzleHttp\Client();

        $n = 0;
//        while (true) {
        $response = $client->request('GET', 'https://www.betonsuccess.ru/sub/66571/ClaudioTT.T.L/picks/', [
            'headers' => [
                'Content-Type'    => 'application/x-www-form-urlencoded',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                'Origin'          => 'https://www.betonsuccess.ru',
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'
            ],
            'proxy'   => '103.254.209.68:3128'
        ]);

        $c = $response->getBody()->getContents();

        preg_match_all('/<div class="event_main">(.*)<\/div>/Us', $c, $matches);

        print $n . PHP_EOL;

        foreach ($matches[1] as $match) {
            print  $match . PHP_EOL;
        }

        print PHP_EOL;

        sleep(1);

        $n++;
//        }
    }

    public function probability()
    {
        $reserved = $this->data('reserved');

        $reservedIsInt = (int)$reserved == $reserved;

        $absProbabilities = [
            1 => 95,
            2 => 95,
            3 => 95,
            4 => 1,
            5 => 1,
            6 => 1,
            7 => 1,
            8 => 1
        ];

        $output['probabilities'] = $this->renderProbabilities($absProbabilities);

        $warehousesCount = rand(2, 4);

        $countByTargetWarehouses = [];

        $lost = $reserved;

        for ($i = 0; $i < $warehousesCount; $i++) {
            if ($i == $warehousesCount - 1) {
                if ($lost) {
                    $countByTargetWarehouses[$i] = $lost;
                }
            } else {
                $count = $this->reserved($reservedIsInt, $lost);

                $lost -= $count;

                if ($lost < 0) {
                    $lost = 0;
                }

                if ($count) {
                    $countByTargetWarehouses[$i] = $count;
                }
            }
        }

        $output['count_by_target_warehouses'] = $countByTargetWarehouses;

        $_ = [];

        foreach ($countByTargetWarehouses as $countByTargetWarehouse) {
            $probabilities = $this->renderProbabilities($absProbabilities);

            $warehouse = $this->getProbabilityWarehouse($probabilities);

            unset($absProbabilities[$warehouse]);

            $_[] = [
                'warehouse' => $warehouse,
                'count'     => $countByTargetWarehouse
            ];
        }

        $output['_'] = $_;

        return $output;
    }

    private function getProbabilityWarehouse($probabilities)
    {
        $rnd = rand(0, 100);

        $sum = 0;
        foreach ($probabilities as $warehouse => $probability) {
            $sum += $probability;

            if ($rnd < $sum) {
                return $warehouse;
            }
        }
    }

    private function renderProbabilities($absProbabilities)
    {
        $probabilitySum = 0;
        $probabilityCount = count($absProbabilities);

        foreach ($absProbabilities as $warehouseId => $probability) {
            $probabilitySum += $probability;
        }

        $output = [];
        foreach ($absProbabilities as $warehouseId => $probability) {
            $output[$warehouseId] = $probability * 100 / $probabilitySum;
        }

        return $output;
    }

    private function reserved($isInt, $lost)
    {
        $rnd = 0.03 * rand(-100, 100) / 100;

        $value = $lost * (0.97 + $rnd);

//        $this->console($rnd);

        if ($isInt) {
            $value = round($value);
        }

        return $value;
    }

    //
    //
    //

    public function own()
    {
        $a = ss()->own->isCatOwn(2, 55048);
        $b = ss()->own->isCatOwn(4, 54236);
    }

    /**
     * @var \ewma\Data\Tree
     */
    private $tree;

    private $output;

    public function tree()
    {
        $this->tree = \ewma\Data\Tree::get(\ss\models\Cat::orderBy('position'));

        $this->treeViewRecursion(35);

        return implode(PHP_EOL, $this->output);
    }

    private $level = 0;

    private function treeViewRecursion($id)
    {
        $node = $this->tree->getNode($id);
        $subnodes = $this->tree->getSubnodes($id);

        $this->output[] = str_repeat('--', $this->level) . ' ' . $node->name;

        if ($subnodes) {
            $this->level++;

            foreach ($subnodes as $subnode) {
                $this->treeViewRecursion($subnode->id);
            }

            $this->level--;
        }
    }

    private $recursiveSplitOutput;

    public function recursiveSplit()
    {
        $this->recursiveSplitRecursion($this->data('string'));

        return $this->recursiveSplitOutput;
    }

    public function recursiveSplitRecursion($string, $layer = 0)
    {
        preg_match_all("/\((([^()]*|(?R))*)\)/", $string, $matches);

        if (count($matches) > 1) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                if (is_string($matches[1][$i])) {
                    if (strlen($matches[1][$i]) > 0) {
                        $this->recursiveSplitOutput[$layer][] = $matches[1][$i];

                        $this->recursiveSplitRecursion($matches[1][$i], $layer + 1);
                    }
                }
            }
        }
    }
}
