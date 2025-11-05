<?php

namespace App\Services\Payment;

/**
 * Backwards-compatible forwarding interface.
 *
 * Code should use the contract in `App\Contracts\Payment\PaymentServiceInterface`.
 * This interface extends that contract to keep older code working while
 * new code should import from the `Contracts` namespace.
 */
interface PaymentServiceInterface extends \App\Contracts\Payment\PaymentServiceInterface {}
