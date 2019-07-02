<?php
namespace MtHash\Model\Transaction;
use MtHash\Model\User\Wallet;
use MtHash\Model\User\WalletRepository;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use MtHash\Model\Asset\Asset;

/**
 * Class Transaction
 * @package MtHash\Model\Transaction
 * @property Wallet $wallet_from
 * @property Wallet $wallet_to
 * @property Recurring $recurring
 */

class Transaction extends Model
{
    use \Timestampable;

    const   NEW         = 1;
    const   FAILED      = 2;
    const   PROCESSED   = 3;

    public $id, $wallet_from_id, $wallet_to_id, $amount, $currency, $condition, $recurring_id;

    private $txManager  = null;

    public function getTxManager()
    {
        if (empty ($this->txManager))
        {
            $this->txManager = (new TxManager())->get();
        }
        return $this->txManager;
    }

    public function transfer (Wallet $from, Wallet $to, float $amount) : Transaction
    {
        $this->setTransaction($this->getTxManager());
        $this->create (
            [
                'wallet_from_id'            => $from->id,
                'wallet_to_id'              => $to->id,
                'amount'                    => $amount,
                'currency'                  => $from->currency,
                'condition'                 => self::NEW,
            ]
        );

        $fee    = new Fee('p2p', $amount);

        $from->canSendTo ($from, $fee->getAmountWithFee());

        $from->setTransaction($this->getTxManager());

        $from->withdraw ($fee->getAmountWithFee());
        $to->deposit($amount);
        $this->applyFee($fee, $this);

        $this->condition    = self::PROCESSED;
        $this->save();

        $this->getTxManager()->commit();

        return $this;
    }

    public function exchange (Wallet $from, Wallet $to, float $amount)
    {
        $exchangeRate   = Asset::calculateExchangeRate(
            $from->asset, $to->asset, $amount
        );

        $tx = $this->getTxManager();

        $this->transfer($from, $to, $amount);
        $this->transfer($to, $from, $amount * $exchangeRate);

        $tx->commit();
    }

    public function applyFee (Fee $fee, Transaction $transaction)
    {
        $serviceWallet  = WalletRepository::getServiceWallet($transaction->currency);
        $serviceWallet+= $fee->getFee();

        switch ($fee->getFrequency())
        {
            case 'now':
                (new Transaction())->create (
                    [
                        'wallet_from_id'            => $transaction->wallet_from_id,
                        'wallet_to_id'              => $serviceWallet->id,
                        'amount'                    => $fee->getFee(),
                        'condition'                 => self::PROCESSED,
                        'currency'                  => 'HASH',
                    ]
                );
            break;

            case 'recurring':
                $recurring  = new Recurring();
                $recurring->save (
                    [
                        'wallet_from_id'            => $transaction->wallet_from_id,
                        'wallet_to_id'              => $serviceWallet->id,
                        'amount'                    => $fee->getFee(),
                        'frequency'                 => $fee->getFrequency(),
                    ]
                );
            break;
        }
    }


}