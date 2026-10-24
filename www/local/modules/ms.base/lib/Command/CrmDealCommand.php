<?php

declare(strict_types=1);

namespace Ms\Base\Command;

use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\Type\DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Bitrix\Crm\DealTable;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Получение сделок за 30дней с суммой по каждой стадии
 */
#[AsCommand(
    name: 'ms-base:crm-deal-export', //
    description: 'CSV Выгрузка сделок за 30 дней (с группировкой по этапам). Сохраняет в папку /var/export/.',
    usages: ['--save-path=./www/upload/'],
)]
class CrmDealCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('save-path', 'sp', InputOption::VALUE_REQUIRED, 'Путь сохранения');
    }

    
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $dir = $input->getOption('save-path') ?? './var/export/';

        $data = "Стадия;Сумма за 30дней\n";
        $headerLen = mb_strlen($data);

        \Bitrix\Main\Loader::includeModule('crm');
        
        $output->writeln('Успешно сформировался заголовок и подключился модуль CRM');

        $query = DealTable::getList([
            'select' => ['TOTAL', 'STAGE_NAME' => 'STAGE_BY.NAME'],
            'filter' => ['>=DATE_CREATE' => (new DateTime())->add('-30days')],
            'runtime' => [
                new ExpressionField('TOTAL', 'SUM(%s)', ['OPPORTUNITY_ACCOUNT'])
            ],
            'group' => ['STAGE_NAME'],
        ]);

        while($result = $query->fetch()) {            
            $data .= sprintf("%s;%s\n",$result['STAGE_NAME'], $result['TOTAL']);
        }

        $output->writeln(sprintf('%s (%s символов)', 'Данные успешно получены', mb_strlen($data) - $headerLen));

        if (!file_exists($dir)) {
            mkdir($dir, recursive: true);
        }

        $filePath = $dir.date('Y_m_d__h_i').'.csv';

        if (!file_put_contents($filePath, $data)){
            $output->writeln('Не удалось записать данные в файл');
            return Command::FAILURE;
        }

        $output->writeln(sprintf('%s - %s', 'Данные выгрузились в файл', $filePath));

        return Command::SUCCESS;
    }
}