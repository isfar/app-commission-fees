<?php

declare(strict_types=1);

namespace Isfar\CommissionTask\Application;

use Evp\Component\Money\Money;
use Isfar\CommissionTask\Commission\CalculatorInterface;
use Isfar\CommissionTask\File\File;
use Isfar\CommissionTask\Money\Currency;
use Isfar\CommissionTask\Money\CurrencyConverter;
use Isfar\CommissionTask\Repository\TransactionRepository;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Isfar\CommissionTask\File\FileReaderInterface;
use Isfar\CommissionTask\Transaction\Mapper\MapperNotFoundException;

class Application
{
    private $commissionCalculator;
    private $currencyConverter;
    private $transactionRepository;
    private $transactionFileImporter;
    private $validator;
    private $exitCodes;

    public function __construct(
        CalculatorInterface $commissionCalculator,
        CurrencyConverter $currencyConverter,
        TransactionRepository $transactionRepository,
        FileReaderInterface $transactionFileImporter,
        ValidatorInterface $validator,
        array $exitCodes
    ) {
        $this->commissionCalculator = $commissionCalculator;
        $this->currencyConverter = $currencyConverter;
        $this->transactionRepository = $transactionRepository;
        $this->transactionFileImporter = $transactionFileImporter;
        $this->validator = $validator;
        $this->exitCodes = $exitCodes;
    }

    /**
     * @param File $file
     *
     * @return string[]
     *
     * @throws UnsupportedFileTypeException If the file type is not supported
     */
    public function run(File $file): array
    {
        try {
            $transactions = $this->transactionFileImporter->read($file);
        } catch (ServiceNotFoundException $serviceNotFoundException) {
            throw new UnsupportedFileTypeException('File type "' . $file->getType() . '" is not supported');
        } catch (MapperNotFoundException $mapperNotFoundException) {
            throw new UnsupportedFileTypeException('No transaction mapper found for the file type: ' . $file->getType());
        }

        $return = [];

        foreach ($transactions as $transaction) {
            $violations = $this->validator->validate($transaction);

            if (count($violations) > 0) {
                exit((int) $this->exitCodes[$violations->get(0)->getPropertyPath()]);
            }

            $transactionInEuro = (clone $transaction)->setMoney(
                $this->currencyConverter->convert($transaction->getMoney(), Currency::EUR)
            );

            /** @var Money */
            $commission = $this->commissionCalculator->calculate($transactionInEuro);

            $this->transactionRepository->store($transactionInEuro);

            $return[] = $this
                ->currencyConverter
                ->convert(
                    $commission,
                    $transaction->getMoney()->getCurrency()
                )
                ->ceil()
                ->round()
                ->getAmount()
            ;
        }

        return $return;
    }
}
