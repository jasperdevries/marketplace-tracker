<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemGraphController extends Controller
{
    public function searchItems(Request $request): array
    {
        $search = $request->get('search');
        $items  = Item::query()
            ->where('market_hash_name', 'like', "%$search%")
            ->get();

        return $items->toArray();
    }

    public function getData(Item $item): array
    {
        $item->load('prices');
        $data = [];
        foreach ($item->prices as $price) {
            $date = $price->created_at->format('Y-m-d');
            if (!isset($data[$date])) {
                $data[$date] = [];
            }
            $data[$date][] = $price->median;
        }
        $data = array_map(function ($prices) {
            return [
                'avg' => round(array_sum($prices) / count($prices), 2)
            ];
        }, $data);

        return [
            'labels'   => array_keys($data),
            'datasets' => [
                [
                    'label'           => 'Avg price',
                    'backgroundColor' => '#665031',
                    'borderColor'     => '#665031',
                    'data'            => array_map(function ($item) {
                        return $item['avg'];
                    }, $data),
                ],
            ],
        ];
    }
}
