<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommandService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Получаем все команды
     *
     * @return array
     */
    public function allCommands(): array
    {
        $commands = $this->entityManager->getRepository(Command::class)->findAll();

        if (!$commands) {
            throw new NotFoundHttpException(
                'Не найдено ни одно команды, выполните docker exec -it tourney_php bin/console 
                doctrine:fixtures:load'
            );
        }

        return $commands;
    }

    /**
     * Возвращает массив ключ-значение (id - имя)
     *
     * @return array
     */
    public function commandsList(): array
    {
        $list = [];
        foreach ($this->allCommands() as $item) {
            $list[$item->getId()] = $item->getName();
        }

        return $list;
    }
}
