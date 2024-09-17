<?php

namespace Capevace\MagicImport\Prompt\Reflection\Examples;

use Capevace\MagicImport\Prompt\Reflection\ArrayOf;
use Capevace\MagicImport\Prompt\Reflection\Description;
use Capevace\MagicImport\Prompt\Reflection\Max;
use Capevace\MagicImport\Prompt\Reflection\Min;

#[Description('A page of a product catalog or brochure')]
class Page
{
    public function __construct(
        #[Description('The page number, starting at 1')]
        #[Min(1)]
        public int $number,

        #[Description('Quick summary of the page')]
        #[Max(1000)]
        public ?string $summary,

        #[Description('List of products on the page')]
        #[ArrayOf(Product::class)]
        #[Min(1)] // Here min refers to the length
        public array $products,

        // #[Description('List of products on the page')]
        // #[ArrayOf('string')]
        // public ?array $brands = null,
    ) {}
}
