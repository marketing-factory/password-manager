<?php

namespace Mfc\PasswordManager\Tests\unit\Command;

use Mfc\PasswordManager\Kernel;
use Mfc\PasswordManager\Service\DataBaseService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class PwpCommandsTest extends KernelTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateUserPwRollout(): void
    {
        $kernel = new Kernel('test', true);
        $application = new Application($kernel);
        $kernel->boot();

        //Testing the create user command
        $command = $application->find('app:create-user');
        $commandTester = new CommandTester($command);
        $username = 'testuser';
        $commandTester->setInputs(['testuser', 'user', 'name', 'egh@marketing-factory.de', 'yes']);
        $commandTester->execute([
            'command'  => $command->getName()
        ]);

        $projectRoot = $kernel->getProjectDir();
        $filePath = $projectRoot . '/data/users/' . $username . '.yaml';
        $filesystem = new Filesystem();

        if (!$filesystem->exists($filePath)) {
            throw new \Exception('Failed to create user', 1571302303);
        }

        //Testing the pwrollout command
        $command = $application->find('app:pw-rollout');
        $commandTester = new CommandTester($command);

        if (getenv('TEST_SYSTEM')) {
            $commandTester->execute([
                'command'  => $command->getName(),
                'system' => getenv('TEST_SYSTEM')
            ]);
        } else {
            $commandTester->execute([
                'command'  => $command->getName()
            ]);
        }
    }
}
