<?php namespace ss\stockPhotoRequest\ui\controllers\queue;

class App extends \Controller
{
    public function capture()
    {
        if ($request = $this->unpackModel('imageable')) {
            $base64 = $this->data('base64');

            $tmpFileName = $this->_protected(k());

            write($tmpFileName);

            $tmpFile = fopen($tmpFileName, 'wb');

            fwrite($tmpFile, base64_decode($base64));
            fclose($tmpFile);

            $ext = 'jpeg';

            rename($tmpFileName, $tmpFileName . '.' . $ext);

            $saver = new \std\images\Saver;

            $saver->targetModel($request)
                ->instance($this->data('instance'))
                ->sourceFile($tmpFileName . '.' . $ext)
                ->outputDir('images')
                ->saveOrigin(true);

            $request->update([
                                 'response_datetime' => \Carbon\Carbon::now()->toDateTimeString(),
                                 'images_cache'      => ''
                             ]);

            (new \ss\stockPhotoRequest\Main)->updateStatusFilterCache($request->tree_id);

            $this->c('<:reload|');

            pusher()->trigger('ss/stockPhotoRequest/capture', [
                'id'        => $request->id,
                'productId' => $request->product_id
            ]);
        }
    }
}
