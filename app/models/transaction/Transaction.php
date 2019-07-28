<?php
namespace MtHash\Model\Transaction;
use MtHash\Model\AbstractModel;
use MtHash\Model\User\User;
use MtHash\Model\User\Wallet;
use MtHash\Model\User\WalletRepository;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use MtHash\Model\Asset\Asset;

/**
 * Class Transaction
 * @package MtHash\Model\Transaction
 * @property Wallet $wallet_from
 * @property Wallet $wallet_to
 * @property Recurring $recurring
 */

class Transaction extends AbstractModel
{
    use \Timestampable;

    const   NEW         = 1;
    const   FAILED      = 2;
    const   PROCESSED   = 3;

    public $id, $wallet_from_id, $wallet_to_id, $amount, $currency, $condition, $recurring_id, $percent, $block_id;

    private $txManager  = null;

    public function initialize()
    {
        $this->belongsTo ('type_id', Type::class, 'id', ['alias' => 'type']);
    }

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
                'type_id'                   => Type::P2P,
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

    public function freeDeposit (Asset $asset, Wallet $to, float $amount, ?int $typeId = null, ?int $blockId = null, ?float $percent = null) : Transaction
    {
        $from   = WalletRepository::getServiceWallet($asset->symbol);

        if (empty ($typeId)) $typeId = Type::BONUS;

        $this->setTransaction($this->getTxManager());
        $this->create (
            [
                'wallet_from_id'            => $from->id,
                'wallet_to_id'              => $to->id,
                'amount'                    => $amount,
                'currency'                  => $from->currency,
                'condition'                 => self::NEW,
                'type_id'                   => $typeId,
                'percent'                   => $percent,
                'block_id'                  => $blockId,
            ]
        );

        $from->canSendTo ($from, $amount);
        $from->setTransaction($this->getTxManager());

        $from->withdraw ($amount);
        $to->deposit($amount);

        $this->condition    = self::PROCESSED;
        $this->save();

        $this->getTxManager()->commit();

        return $this;
    }

    public function exchange (Wallet $from, Asset $asset, float $amount)
    {
        if ($asset->symbol == $from->currency) throw new \BusinessLogicException('You can not exchange same currency');

        $serviceWalletTo    = WalletRepository::getServiceWallet($from->asset->symbol);
        $serviceWalletFrom  = WalletRepository::getServiceWallet($asset->symbol);
        $to                 = WalletRepository::byUserWithAsset($from->user, $asset);

        $exchangeRate   = Asset::calculateExchangeRate(
            $from->asset, $asset, $amount
        );

        $exchangedAmount    = $amount * $exchangeRate;

        $from->canSendTo($serviceWalletTo, $amount);
        $serviceWalletFrom->canSendTo($to, $exchangedAmount);

        $this->setTransaction($this->getTxManager());

        $fromUserToService  = new self;
        $fromUserToService->create (
            [
                'wallet_from_id'            => $from->id,
                'wallet_to_id'              => $serviceWalletTo->id,
                'amount'                    => $amount,
                'currency'                  => $from->currency,
                'condition'                 => self::NEW,
                'type_id'                   => Type::EXCHANGE,
            ]
        );

        $from->withdraw($amount);

        $fromServiceToUser  = new self;
        $fromServiceToUser->create(
            [
                'wallet_from_id'            => $serviceWalletFrom->id,
                'wallet_to_id'              => $to->id,
                'amount'                    => $exchangedAmount,
                'currency'                  => $serviceWalletFrom->currency,
                'condition'                 => self::NEW,
                'type_id'                   => Type::EXCHANGE,
            ]
        );

        $to->deposit ($exchangedAmount);

        $this->getTxManager()->commit();

        $fromUserToService->condition   = self::PROCESSED;
        $fromServiceToUser->condition   = self::PROCESSED;

        $fromServiceToUser->save();
        $fromUserToService->save();

        return true;
    }

    public function applyFee (Fee $fee, Transaction $transaction)
    {
        $serviceWallet  = WalletRepository::getServiceWallet($transaction->currency);
        $serviceWallet->deposit($fee->getFee());

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
                        'type_id'                   => Type::FEE,
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
                        'type_id'                   => Type::FEE,
                    ]
                );
            break;
        }
    }

    static public function calculateRevenueForPeriod (Asset $asset, User $user)
    {

    }


}