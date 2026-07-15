<?php

namespace Database\Seeders;

use App\Models\Configuration;
use App\Models\ManualSale;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoTransactionSeeder extends Seeder
{
    /**
     * Mengisi data transaksi contoh agar Dashboard (F-14), Pesanan
     * Website (F-06), Penjualan Manual (F-10), dan Laporan Keuangan
     * (F-13) memiliki data untuk didemonstrasikan langsung setelah
     * instalasi, tanpa perlu input manual terlebih dahulu.
     */
    public function run(): void
    {
        $p = fn (string $brand, string $name) => Product::where('brand', $brand)->where('name', $name)->first();

        $airForce1 = $p('Nike', "Air Force 1 '07");
        $oldSkool = $p('Vans', 'Old Skool Black');
        $dunkLow = $p('Nike', 'Dunk Low Panda');
        $sambaOg = $p('Adidas', 'Samba OG');
        $pegasus = $p('Nike', 'Pegasus 41');
        $gazelle = $p('Adidas', 'Gazelle Indoor');
        $timberland = $p('Timberland', '6-Inch Premium');
        $salomon = $p('Salomon', 'XT-6 Trail');

        // ------------------------------------------------------------
        // Pesanan Website - PENDING (menunggu verifikasi Admin) (F-06)
        // ------------------------------------------------------------
        if ($airForce1 && $oldSkool) {
            $this->makeOrder([
                'customer_name' => 'Rangga Pratama',
                'address' => 'Jl. Melati No. 10, Margahayu, Kabupaten Bandung',
                'whatsapp' => '081234567890',
            ], [[$airForce1, 1], [$oldSkool, 1]]);
        }

        if ($dunkLow) {
            $this->makeOrder([
                'customer_name' => 'Sinta Dewi',
                'address' => 'Jl. Kenanga No. 5, Sayati, Kabupaten Bandung',
                'whatsapp' => '085611223344',
            ], [[$dunkLow, 1]]);
        }

        if ($sambaOg) {
            $this->makeOrder([
                'customer_name' => 'Budi Hartono',
                'address' => 'Jl. Anggrek No. 21, Margahayu, Kabupaten Bandung',
                'whatsapp' => '081399887766',
            ], [[$sambaOg, 1]]);
        }

        // ------------------------------------------------------------
        // Pesanan Website - APPROVED (riwayat, untuk laporan) (F-07, F-11, F-12)
        // ------------------------------------------------------------
        if ($pegasus) {
            $this->makeApprovedOrder([
                'customer_name' => 'Dimas Aditya',
                'address' => 'Jl. Cihampelas No. 8, Bandung',
                'whatsapp' => '087712345678',
            ], [[$pegasus, 1]], now()->subDays(5));
        }

        if ($gazelle && $timberland) {
            $this->makeApprovedOrder([
                'customer_name' => 'Putri Lestari',
                'address' => 'Jl. Sukajadi No. 100, Bandung',
                'whatsapp' => '081288776655',
            ], [[$gazelle, 1], [$timberland, 1]], now()->subDays(2));
        }

        // ------------------------------------------------------------
        // Penjualan Manual - WA / IG / FB / Walk-in (F-10, F-11, F-12)
        // ------------------------------------------------------------
        if ($oldSkool) {
            ManualSale::record($oldSkool, 2, (float) $oldSkool->price, 'Walk-in', null, now()->subDays(6)->toDateString());
        }

        if ($airForce1) {
            ManualSale::record($airForce1, 1, (float) $airForce1->price, 'WhatsApp', 'Dimas - WA', now()->subDays(4)->toDateString());
        }

        if ($dunkLow) {
            ManualSale::record($dunkLow, 1, (float) $dunkLow->price, 'WhatsApp', null, now()->subDays(3)->toDateString());
        }

        if ($gazelle) {
            ManualSale::record($gazelle, 1, (float) $gazelle->price, 'Instagram', 'Dimas - IG @dimasstyle', now()->subDays(1)->toDateString());
        }

        if ($salomon) {
            ManualSale::record($salomon, 1, (float) $salomon->price, 'Facebook', null, now()->toDateString());
        }
    }

    /**
     * Membuat pesanan website dengan status pending beserta detail itemnya.
     *
     * @param  array<int, array{0: Product, 1: int}>  $items
     */
    protected function makeOrder(array $attributes, array $items): Order
    {
        $subtotal = 0;
        $detailRows = [];

        foreach ($items as [$product, $qty]) {
            $price = (float) $product->price;
            $subtotal += $price * $qty;

            $detailRows[] = [
                'product_id' => $product->id,
                'product_name' => $product->full_name,
                'quantity' => $qty,
                'price' => $price,
            ];
        }

        $shipping = (float) Configuration::get('shipping_cost', '0');

        $order = Order::create(array_merge($attributes, [
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'total' => $subtotal + $shipping,
            'status' => 'pending',
        ]));

        $order->details()->createMany($detailRows);

        return $order;
    }

    /**
     * Membuat pesanan dengan status pending, lalu langsung menyetujuinya
     * (memicu pengurangan stok & snapshot HPP), kemudian mengatur ulang
     * tanggal pesanan agar tersebar untuk kebutuhan grafik & laporan.
     *
     * @param  array<int, array{0: Product, 1: int}>  $items
     */
    protected function makeApprovedOrder(array $attributes, array $items, Carbon $date): Order
    {
        $order = $this->makeOrder($attributes, $items);
        $order->load('details.product');
        $order->approve();

        $order->timestamps = false;
        $order->forceFill([
            'created_at' => $date,
            'updated_at' => $date,
            'approved_at' => $date,
        ])->save();
        $order->timestamps = true;

        return $order;
    }
}
