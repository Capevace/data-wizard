<?php

namespace App\Filament\Resources\ExtractionBucketResource\Widgets;

use App\Filament\Resources\ExtractionBucketResource\Widgets\Concerns\HasBucketForEvaluation;
use App\Models\ExtractionRun;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CorrectnessChart extends ChartWidget
{
    use HasBucketForEvaluation;

    protected static ?string $heading = 'Custom average';

    protected function getData(): array
    {
        $validation_data = json_decode(<<<JSON
        {
          "lineItems": [
            {
              "position": 1,
              "quantity": 3,
              "unitPrice": 43.2,
              "netAmount": 129.6,
              "vatRate": 19
            },
            {
              "quantity": 1,
              "vatRate": 19,
              "position": 2,
              "unitPrice": 122.5,
              "netAmount": 122.5
            }
          ],
          "invoiceNumber": "181301674",
          "seller": {
            "address": "Erfurter Strasse 13",
            "city": "Demoort",
            "postalCode": "74465",
            "country": "DE",
            "vatNumber": "DE136695976",
            "name": "ELEKTRON Industrieservice GmbH"
          },
          "currency": "EUR",
          "paymentDetails": {
            "paymentTerms": "Zahlbar sofort rein netto",
            "paymentMethod": "SEPA_TRANSFER"
          },
          "buyer": {
            "city": "Karlsruhe",
            "address": "Musterstr. 18",
            "country": "DE",
            "customerNumber": "16259",
            "name": "ConsultingService GmbH",
            "postalCode": "76138"
          },
          "totalAmounts": {
            "dueTotal": 300,
            "taxTotal": 47.9,
            "netTotal": 252.1,
            "grossTotal": 300
          },
          "issueDate": "2018-04-25"
        }
        JSON, true);

        [$strategies, $datasets] = $this->groupDataByStrategy(function (ExtractionRun $run) use ($validation_data) {
            $invoice = $run->data;

            // Count the number of errors spotted. Go through every single property (included nested ones) and check if they are equal.
            $errors = 0;
            $all_errors = [];

            $compare = function ($a, $b, $key) use (&$compare, &$errors, &$all_errors) {
                if (is_array($a) && is_array($b)) {
                    foreach ($a as $key => $value) {
                        if (!Arr::has($b, $key) && !in_array($key, ['name', 'description'])) {
                            $errors++;
                            $all_errors[] = ['key' => $key, 'value' => $value, 'expected' => null];
                            continue;
                        } else if (!Arr::has($b, $key)) {
                            continue;
                        }

                        $compare($value, $b[$key], $key);
                    }
                } else {
                    // We don't check text values for abslute correctness
                    if ($a !== $b && !in_array($key, ['name', 'description', 'paymentTerms'])) {
                        $all_errors[] = ['key' => $key, 'value' => $a, 'expected' => $b];
                        $errors++;
                    }
                }
            };

            $compare($invoice, $validation_data, 'invoice');

            if ($errors > 0) {
//                dd($all_errors, $run);
            }

            return $errors;
        }, method: 'average');

        return [
            'labels' => $strategies
                ->map(fn (string $strategy) => Str::title($strategy))
                ->values()
                ->toArray(),
            'datasets' => $datasets->values()->toArray()
        ];
    }

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<JS
            {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const count = tooltipItem.dataset.counts[tooltipItem.dataIndex];
                                const runLabel = count === 1 ? 'run' : 'runs';

                                return `\${tooltipItem.dataset.label}: \${tooltipItem.raw.toFixed(2)} seconds / \${count} \${runLabel}`;
                            }
                        },
                    }
                },
            }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
