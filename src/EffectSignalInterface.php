<?php

declare(strict_types=1);

namespace Zodimo\FRP;

use Zodimo\BaseReturn\IOMonad;
use Zodimo\BaseReturn\Option;

/**
 * @template TVALUE
 * @template TFAILURE
 *
 * @template-extends DerivedSignalInterface<Option<IOMonad<TVALUE,TFAILURE>>>
 */
interface EffectSignalInterface extends DerivedSignalInterface {}
