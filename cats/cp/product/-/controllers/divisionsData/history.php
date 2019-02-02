<?php namespace ss\cats\cp\product\controllers\divisionsData;

class History extends \Controller
{
    private $product;

    public function __create()
    {
        $this->product = $this->unpackModel('product') or $this->lock();
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $product = $this->product;

        $type = $this->data('type');
        $id = $this->data('id');

        if ($type == 'division') {
            $pivot = \ss\multisource\models\ProductDivision::where('product_id', $product->id)
                ->where('division_id', $id)
                ->first();

            if ($pivot) {
                $history = $pivot->history()
                    ->orderBy('datetime', 'DESC')
                    ->get();

                foreach ($history as $row) {
                    $v->assign('division_row', [
                        'DATETIME' => \Carbon\Carbon::parse($row->datetime)->format('d.m.Y H:i:s'),
                        'PRICE'    => $row->price
                    ]);
                }
            }
        }

        if ($type == 'warehouse') {
            $pivot = \ss\multisource\models\ProductWarehouse::where('product_id', $product->id)
                ->where('warehouse_id', $id)
                ->first();

            if ($pivot) {
                $history = $pivot->history()
                    ->orderBy('datetime', 'DESC')
                    ->get();

                foreach ($history as $row) {
                    $v->assign('warehouse_row', [
                        'DATETIME' => \Carbon\Carbon::parse($row->datetime)->format('d.m.Y H:i:s'),
                        'STOCK'    => $row->stock ?? '-',
                        'RESERVED' => $row->reserved ?? '-'
                    ]);
                }
            }
        }

        $v->assign([
                       'CONTENT' => false
                   ]);

        $this->css();

        return $v;
    }
}
