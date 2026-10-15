<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Exceptions;

use RuntimeException;

/**
 * The courier refused the shipment or did not answer. The message says why, for the person
 * who asked for the waybill.
 */
final class WaybillNotCreated extends RuntimeException
{
}
