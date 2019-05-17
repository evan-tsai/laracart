<?php


namespace EvanTsai\Laracart\Modules\Traits;


use EvanTsai\Laracart\Gateways\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait Checkout
{
    public function checkout(Request $request)
    {
        [$gateway, $paymentMethod] = $this->validateRequest($request);

        $this->order->payment_gateway = $gateway;
        $this->order->payment_method = $paymentMethod;
        $this->order->save();

        $gatewayClass = $this->getGatewayClass($gateway);

        return $gatewayClass->checkout($this->order);
    }

    protected function validateRequest($request)
    {
        $availableGateways = config('laracart.available_gateways');

        $request->validate([
            'gateway' => [
                'sometimes',
                Rule::in($availableGateways),
            ],
            'payment_method' => 'nullable|string',
        ]);

        return [
            $request->input('gateway', $availableGateways[0]),
            $request->input('payment_method', \ECPay_PaymentMethod::ALL),
        ];
    }

    protected function getGatewayClass($gateway)
    {
        $gatewayClassName = config('laracart.gateways.' . $gateway . '.class');
        $gatewayClass = new $gatewayClassName;

        if (!$gatewayClass instanceof PaymentGateway) {
            throw new \UnexpectedValueException($gatewayClassName . ' is not a Payment Gateway');
        }

        return $gatewayClass;
    }
}